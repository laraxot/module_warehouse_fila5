<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Warehouse\Models\Location;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Tabella `warehouse_locations` (modello Location). Fino al 2026-11-05 si chiamava `locations` e collideva
 * con la tabella `locations` del modulo Geo sullo stesso database fisico: chi migrava per secondo trovava la
 * tabella dell'altro modulo e `tableCreate` la saltava. Per questo la migrazione ha un timestamp nuovo
 * (gira anche dove la vecchia `create_locations_table` risulta già eseguita) e adotta la vecchia tabella
 * solo se è davvero quella di Warehouse (ha la colonna `area_id`, che Geo non ha).
 *
 * `code` è univoco nel magazzino (non per area): il vincolo attraversa `areas` e lo
 * garantisce il modello Location (evento saving), non un indice.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = Location::class;

    private const string LEGACY_TABLE = 'locations';

    public function up(): void
    {
        $this->adoptLegacyWarehouseTable();

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

    private function adoptLegacyWarehouseTable(): void
    {
        $schema = $this->getConn();
        if ($schema->hasTable($this->getTable()) || ! $schema->hasTable(self::LEGACY_TABLE)) {
            return;
        }

        if ($schema->hasColumn(self::LEGACY_TABLE, 'area_id')) {
            $schema->rename(self::LEGACY_TABLE, $this->getTable());
        }
    }
};
