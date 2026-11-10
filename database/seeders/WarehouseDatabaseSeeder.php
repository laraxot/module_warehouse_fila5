<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestratore Warehouse: magazzino demo con aree e posizioni, poi alcune unità demo.
 */
class WarehouseDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WarehousePermissionSeeder::class,
            WarehouseSeeder::class,
            StockUnitSeeder::class,
        ]);
    }
}
