<?php

declare(strict_types=1);

use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/**
 * Indici di supporto alle query di riepilogo di Warehouse (audit database R14), sulla connessione `warehouse`.
 *
 * Solo aggiunte idempotenti (XotBaseMigration::ensureIndexes): un indice già presente con lo stesso nome o sulle
 * stesse colonne non viene duplicato. Niente down(): togliere indici in produzione è una decisione dell'utente.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = StockUnit::class;

    public function up(): void
    {
        $this->ensureIndexes([
            // Riepiloghi per magazzino (Roosko GetPlantStockSummaryAction, widget di occupazione):
            // warehouse_id = ? AND status IN (...) AND location_id IS NOT NULL.
            (new StockUnit)->getTable() => [
                'stock_units_warehouse_status_location_index' => ['warehouse_id', 'status', 'location_id'],
            ],
        ]);
    }
};
