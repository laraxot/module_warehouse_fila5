<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    protected ?string $model_class = Warehouse::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('code', 32)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('map_width_m', 10, 2)->nullable();
            $table->decimal('map_height_m', 10, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('description')) {
                $table->text('description')->nullable();
            }

            if (! $this->hasColumn('map_width_m')) {
                $table->decimal('map_width_m', 10, 2)->nullable();
            }

            if (! $this->hasColumn('map_height_m')) {
                $table->decimal('map_height_m', 10, 2)->nullable();
            }

            if (! $this->hasColumn('is_active')) {
                $table->boolean('is_active')->default(true)->index();
            }

            $this->updateTimestamps($table, true);
        });
    }
};
