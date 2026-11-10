<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

test('the stock units summary index exists and the migration is idempotent', function (): void {
    $migration = require dirname(__DIR__, 2).'/database/migrations/2026_11_06_100500_add_query_supporting_indexes.php';

    if (! \is_object($migration) || ! method_exists($migration, 'up')) {
        throw new LogicException('La migrazione degli indici deve restituire un oggetto con up().');
    }

    $migration->up();
    $migration->up();

    Assert::assertTrue(Schema::connection('warehouse')->hasIndex((new StockUnit)->getTable(), ['warehouse_id', 'status', 'location_id']));
});

test('locations live in warehouse_locations, not in the Geo locations table', function (): void {
    Assert::assertSame('warehouse_locations', (new Location)->getTable());
    Assert::assertTrue(Schema::connection('warehouse')->hasColumn('warehouse_locations', 'area_id'));
});
