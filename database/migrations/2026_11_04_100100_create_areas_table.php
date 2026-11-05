<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    protected ?string $model_class = Area::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->string('code', 32);
            $table->string('name');
            $table->string('type', 32)->index();
            $table->decimal('max_weight_kg', 14, 3)->nullable();
            $table->json('map_geometry')->nullable();
            $table->string('color', 16)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unique(['warehouse_id', 'code']);
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('max_weight_kg')) {
                $table->decimal('max_weight_kg', 14, 3)->nullable();
            }

            if (! $this->hasColumn('map_geometry')) {
                $table->json('map_geometry')->nullable();
            }

            if (! $this->hasColumn('color')) {
                $table->string('color', 16)->nullable();
            }

            if (! $this->hasColumn('is_active')) {
                $table->boolean('is_active')->default(true)->index();
            }

            $this->updateTimestamps($table, true);
        });
    }
};
