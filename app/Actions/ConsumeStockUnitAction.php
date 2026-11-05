<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Datas\CapacityCheckData;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Exceptions\InvalidStockQuantityException;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Consumo parziale o totale di una unità (es. input di una lavorazione).
 *
 * Il peso netto scende della quantità consumata; a peso 0 l'unità passa a `consumed`
 * e lascia la posizione.
 */
class ConsumeStockUnitAction
{
    use QueueableAction;

    public function __construct(
        private readonly RecordStockMovementAction $recordMovement,
        private readonly LockStockUnitAction $lockStockUnit,
    ) {}

    public function execute(StockUnit $unit, float $kg, ?Model $reference = null): StockMovement
    {
        $kg = round($kg, 3);
        if ($kg <= 0.0) {
            throw InvalidStockQuantityException::notPositive($kg);
        }

        return $unit->getConnection()->transaction(function () use ($unit, $kg, $reference): StockMovement {
            $locked = $this->lockStockUnit->execute($unit);

            Assert::true(
                $locked->status->canTransitionTo(StockUnitStatusEnum::CONSUMED),
                $locked->transitionDeniedMessage(StockUnitStatusEnum::CONSUMED),
            );

            $available = $locked->netWeightKg();
            if ($kg > $available + CapacityCheckData::EPSILON_KG) {
                throw InvalidStockQuantityException::exceedsStock($locked, $kg);
            }

            // Il residuo sotto la tolleranza di arrotondamento si considera consumato.
            $kg = min($kg, $available);

            $movement = $this->recordMovement->execute($locked, MovementTypeEnum::CONSUMPTION, $kg, $locked->location, null, $reference);

            if ($locked->netWeightKg() <= CapacityCheckData::EPSILON_KG) {
                $locked->status = StockUnitStatusEnum::CONSUMED;
                $locked->location()->dissociate();
                $locked->save();
            }

            $unit->syncFrom($locked);

            return $movement;
        });
    }
}
