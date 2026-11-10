<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Modules\Warehouse\Actions\BuildWarehouseMapAction;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Actions\GetAreaLoadKgAction;
use Modules\Warehouse\Actions\GetAreaOccupancyAction;
use Modules\Warehouse\Actions\GetLocationLoadKgAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/** Magazzino con `$areas` aree, una posizione per area e un'unità da 10 kg per posizione. */
function loadQueriesWarehouse(int $areas): Warehouse
{
    $warehouse = Warehouse::factory()->createOne();
    for ($i = 0; $i < $areas; $i++) {
        $area = Area::factory()->createOne(['warehouse_id' => $warehouse->id, 'max_weight_kg' => 100]);
        $location = Location::factory()->createOne(['area_id' => $area->id]);
        app(CreateStockUnitAction::class)->execute(new StockUnitData(
            warehouseId: $warehouse->id,
            containerType: ContainerTypeEnum::BOX,
            netWeightKg: 10.0,
            locationId: $location->id,
        ));
    }

    return $warehouse;
}

function countQueries(Closure $callback): int
{
    DB::connection('warehouse')->flushQueryLog();
    DB::connection('warehouse')->enableQueryLog();
    $callback();
    $count = count(DB::connection('warehouse')->getQueryLog());
    DB::connection('warehouse')->disableQueryLog();

    return $count;
}

test('areas loaded withLoadKg give the same load without one query per row', function (): void {
    $warehouse = loadQueriesWarehouse(3);

    $queries = countQueries(static function () use ($warehouse): void {
        $areas = Area::query()->withLoadKg()->where('warehouse_id', $warehouse->id)->get();
        foreach ($areas as $area) {
            Assert::assertSame(10.0, app(GetAreaOccupancyAction::class)->execute($area)->loadKg);
            Assert::assertSame(10.0, app(GetAreaLoadKgAction::class)->execute($area));
        }
    });

    Assert::assertSame(1, $queries);
});

test('locations loaded withLoadKg give the same load without one query per row', function (): void {
    $warehouse = loadQueriesWarehouse(3);
    $areaIds = Area::query()->where('warehouse_id', $warehouse->id)->pluck('id')->all();

    $queries = countQueries(static function () use ($areaIds): void {
        foreach (Location::query()->withLoadKg()->whereIn('area_id', $areaIds)->get() as $location) {
            Assert::assertSame(10.0, app(GetLocationLoadKgAction::class)->execute($location));
        }
    });

    Assert::assertSame(1, $queries);
});

test('an empty location loaded withLoadKg has zero load', function (): void {
    $location = Location::factory()->createOne();

    $loaded = Location::query()->withLoadKg()->whereKey($location->id)->firstOrFail();

    Assert::assertSame(0.0, $loaded->preloadedLoadKg());
});

test('the locking read ignores the preloaded value', function (): void {
    $warehouse = loadQueriesWarehouse(1);
    $area = Area::query()->withLoadKg()->where('warehouse_id', $warehouse->id)->firstOrFail();
    $area->setAttribute(Area::LOAD_KG_ATTRIBUTE, '999');

    Assert::assertSame(999.0, app(GetAreaLoadKgAction::class)->execute($area));
    Assert::assertSame(10.0, app(GetAreaLoadKgAction::class)->execute($area, lockingRead: true));
});

test('the warehouse map runs the same number of queries whatever the number of areas', function (): void {
    $small = loadQueriesWarehouse(1);
    $large = loadQueriesWarehouse(4);

    $smallQueries = countQueries(static fn (): mixed => app(BuildWarehouseMapAction::class)->execute($small));
    $map = null;
    $largeQueries = countQueries(static function () use ($large, &$map): void {
        $map = app(BuildWarehouseMapAction::class)->execute($large);
    });

    Assert::assertSame($smallQueries, $largeQueries);
    Assert::assertNotNull($map);
    foreach ($map->areas as $area) {
        Assert::assertSame(10.0, $area->occupancy->loadKg);
    }
});
