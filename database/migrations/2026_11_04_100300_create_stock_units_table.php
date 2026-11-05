<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * `subject_type`/`subject_id`: morph cross-modulo (es. WasteLot di Roosko) → solo indice, nessun vincolo.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = StockUnit::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->string('code', 32)->unique();
            $table->string('container_type', 32)->index();
            $table->string('status', 32)->index();
            $table->unsignedBigInteger('location_id')->nullable()->index();
            $table->decimal('net_weight_kg', 14, 3)->default(0);
            $table->decimal('tare_weight_kg', 14, 3)->nullable();
            $table->nullableMorphs('subject');
            $table->timestamp('received_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->json('extra_attributes')->nullable();
            $table->index(['location_id', 'status']);
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('tare_weight_kg')) {
                $table->decimal('tare_weight_kg', 14, 3)->nullable();
            }

            if (! $this->hasColumn('subject_type')) {
                $table->nullableMorphs('subject');
            }

            if (! $this->hasColumn('received_at')) {
                $table->timestamp('received_at')->nullable()->index();
            }

            if (! $this->hasColumn('notes')) {
                $table->text('notes')->nullable();
            }

            if (! $this->hasColumn('extra_attributes')) {
                $table->json('extra_attributes')->nullable();
            }

            $this->updateTimestamps($table, true);
        });
    }
};
