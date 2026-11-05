<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Warehouse\Models\Location;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * `code` è univoco nel magazzino (non per area): il vincolo attraversa `areas` e lo
 * garantisce il modello Location (evento saving), non un indice.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = Location::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('area_id')->index();
            $table->string('code', 64)->index();
            $table->string('name')->nullable();
            $table->decimal('x', 10, 2)->nullable();
            $table->decimal('y', 10, 2)->nullable();
            $table->decimal('max_weight_kg', 14, 3)->nullable();
            $table->boolean('is_active')->default(true)->index();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('name')) {
                $table->string('name')->nullable();
            }

            if (! $this->hasColumn('x')) {
                $table->decimal('x', 10, 2)->nullable();
            }

            if (! $this->hasColumn('y')) {
                $table->decimal('y', 10, 2)->nullable();
            }

            if (! $this->hasColumn('max_weight_kg')) {
                $table->decimal('max_weight_kg', 14, 3)->nullable();
            }

            if (! $this->hasColumn('is_active')) {
                $table->boolean('is_active')->default(true)->index();
            }

            $this->updateTimestamps($table, true);
        });
    }
};
