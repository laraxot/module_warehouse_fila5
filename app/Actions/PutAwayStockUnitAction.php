<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Exceptions\LocationMismatchException;
use Modules\Warehouse\Exceptions\PlacementDeniedException;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Posiziona (putaway) o sposta (transfer) una unità in una posizione dello stesso magazzino.
 *
 * Verifica i guard di posizionamento (CheckPlacementAction): un rifiuto lancia
 * PlacementDeniedException con il motivo del guard. Lo stato diventa `stored`.
 *
 * Concorrenza: nella stessa transazione blocca prima area e posizione di destinazione
 * (LockPlacementTargetAction), poi l'unità (LockStockUnitAction), e solo dopo esegue i guard.
 * Due posizionamenti paralleli verso la stessa area/posizione si serializzano: il secondo legge
 * il carico dopo il commit del primo e non supera la portata. I guard ricevono la posizione
 * bloccata (con `area` bloccata già caricata) e girano dentro la transazione `warehouse`.
 */
class PutAwayStockUnitAction
{
    use QueueableAction;

    public function __construct(
        private readonly CheckPlacementAction $checkPlacement,
        private readonly RecordStockMovementAction $recordMovement,
        private readonly LockStockUnitAction $lockStockUnit,
        private readonly LockPlacementTargetAction $lockPlacementTarget,
    ) {}

    public function execute(StockUnit $unit, Location $location, ?Model $reference = null): StockUnit
    {
        return $unit->getConnection()->transaction(function () use ($unit, $location, $reference): StockUnit {
            // Ordine fisso dei lock: area → posizione → unità (vedi LockPlacementTargetAction).
            $location = $this->lockPlacementTarget->execute($location);
            $locked = $this->lockStockUnit->execute($unit);

            Assert::true(
                $locked->status->canTransitionTo(StockUnitStatusEnum::STORED),
                $locked->transitionDeniedMessage(StockUnitStatusEnum::STORED),
            );

            if ($location->warehouseId() !== $locked->warehouse_id) {
                throw LocationMismatchException::otherWarehouse($locked, $location);
            }

            if (! $location->is_active) {
                throw LocationMismatchException::inactive($location);
            }

            if ($locked->location_id === $location->id) {
                throw LocationMismatchException::sameLocation($locked, $location);
            }

            $reason = $this->checkPlacement->execute($locked, $location);
            if ($reason !== null) {
                throw PlacementDeniedException::because($reason, $locked, $location);
            }

            $from = $locked->location;
            $type = $from === null ? MovementTypeEnum::PUTAWAY : MovementTypeEnum::TRANSFER;

            $this->recordMovement->execute($locked, $type, $locked->netWeightKg(), $from, $location, $reference);

            $locked->status = StockUnitStatusEnum::STORED;
            $locked->save();

            $unit->syncFrom($locked);

            return $unit;
        });
    }
}
