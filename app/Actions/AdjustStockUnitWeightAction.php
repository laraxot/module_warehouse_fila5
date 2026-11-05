<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Datas\CapacityCheckData;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Exceptions\InvalidStockQuantityException;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Rettifica del peso netto (ripesata, calo, errore di registrazione) con motivo obbligatorio.
 *
 * Il movimento `adjustment` registra la differenza con segno. La rettifica descrive la realtà
 * fisica: non passa dai guard di posizionamento.
 */
class AdjustStockUnitWeightAction
{
    use QueueableAction;

    public function __construct(
        private readonly RecordStockMovementAction $recordMovement,
        private readonly LockStockUnitAction $lockStockUnit,
    ) {}

    public function execute(StockUnit $unit, float $newNetKg, string $reason): StockMovement
    {
        $reason = trim($reason);
        Assert::stringNotEmpty($reason, SafeStringCastAction::cast(__('warehouse::exceptions.reason_required')));

        $newNetKg = round($newNetKg, 3);
        if ($newNetKg < 0.0) {
            throw InvalidStockQuantityException::negative($newNetKg);
        }

        return $unit->getConnection()->transaction(function () use ($unit, $newNetKg, $reason): StockMovement {
            $locked = $this->lockStockUnit->execute($unit);

            Assert::true(
                $locked->status->isInStock(),
                SafeStringCastAction::cast(__('warehouse::exceptions.invalid_state.not_in_stock', ['code' => $locked->code])),
            );

            $delta = round($newNetKg - $locked->netWeightKg(), 3);
            if (abs($delta) < CapacityCheckData::EPSILON_KG) {
                throw InvalidStockQuantityException::unchanged($locked);
            }

            $movement = $this->recordMovement->execute($locked, MovementTypeEnum::ADJUSTMENT, $delta, $locked->location, $locked->location, null, $reason);

            $unit->syncFrom($locked);

            return $movement;
        });
    }
}
