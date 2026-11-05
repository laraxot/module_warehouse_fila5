<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Uscita dell'unità intera dal magazzino (status `shipped`, nessuna posizione).
 * Il peso netto dell'unità resta quello spedito; il movimento porta la giacenza a 0.
 */
class ShipStockUnitAction
{
    use QueueableAction;

    public function __construct(
        private readonly RecordStockMovementAction $recordMovement,
        private readonly LockStockUnitAction $lockStockUnit,
    ) {}

    public function execute(StockUnit $unit, ?Model $reference = null): StockMovement
    {
        return $unit->getConnection()->transaction(function () use ($unit, $reference): StockMovement {
            $locked = $this->lockStockUnit->execute($unit);

            Assert::true(
                $locked->status->canTransitionTo(StockUnitStatusEnum::SHIPPED),
                $locked->transitionDeniedMessage(StockUnitStatusEnum::SHIPPED),
            );

            $movement = $this->recordMovement->execute($locked, MovementTypeEnum::SHIPMENT, $locked->netWeightKg(), $locked->location, null, $reference);

            $locked->status = StockUnitStatusEnum::SHIPPED;
            $locked->save();

            $unit->syncFrom($locked);

            return $movement;
        });
    }
}
