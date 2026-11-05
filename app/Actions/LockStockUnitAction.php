<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Rilegge l'unità con `lockForUpdate` (da chiamare dentro una transazione): le action che
 * cambiano peso, posizione o stato lavorano sempre sull'ultima versione salvata.
 */
class LockStockUnitAction
{
    use QueueableAction;

    public function execute(StockUnit $unit): StockUnit
    {
        return StockUnit::query()
            ->whereKey($unit->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }
}
