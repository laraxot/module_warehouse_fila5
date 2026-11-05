<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Warehouse\Models\StockMovement;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Registro immutabile dei movimenti: niente soft delete.
 * `reference_type`/`reference_id`: morph cross-modulo (ordine di lavorazione, conferimento, uscita…) → solo indice.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = StockMovement::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_unit_id')->index();
            $table->string('type', 32)->index();
            $table->decimal('quantity_kg', 14, 3);
            $table->decimal('weight_before_kg', 14, 3);
            $table->decimal('weight_after_kg', 14, 3);
            $table->unsignedBigInteger('from_location_id')->nullable()->index();
            $table->unsignedBigInteger('to_location_id')->nullable()->index();
            $table->nullableMorphs('reference');
            $table->timestamp('occurred_at')->index();
            $table->text('notes')->nullable();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('reference_type')) {
                $table->nullableMorphs('reference');
            }

            if (! $this->hasColumn('notes')) {
                $table->text('notes')->nullable();
            }

            $this->updateTimestamps($table, false);
        });
    }
};
