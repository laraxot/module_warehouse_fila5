<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Actions\GetAreaLoadKgAction;
use Modules\Warehouse\Actions\GetLocationLoadKgAction;
use Modules\Warehouse\Actions\LockPlacementTargetAction;
use Modules\Warehouse\Actions\PutAwayStockUnitAction;
use Modules\Warehouse\Contracts\PlacementGuardContract;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Exceptions\PlacementDeniedException;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/** Registro condiviso fra la action di lock spia e il guard spia. */
final class PlacementLockJournal
{
    /** @var list<string> */
    public static array $events = [];

    public static bool $guardInTransaction = false;

    public static bool $guardSawLockedArea = false;
}

/** Spia: registra quando Warehouse blocca area e posizione di destinazione. */
final class RecordingLockPlacementTargetAction extends LockPlacementTargetAction
{
    public function execute(Location $location): Location
    {
        PlacementLockJournal::$events[] = 'lock_target';

        return parent::execute($location);
    }
}

/** Guard spia: registra il momento della chiamata e cosa vede. */
final class RecordingPlacementGuard implements PlacementGuardContract
{
    public function deny(StockUnit $unit, Location $location): ?string
    {
        PlacementLockJournal::$events[] = 'guard';
        PlacementLockJournal::$guardInTransaction = DB::connection('warehouse')->transactionLevel() > 0;
        PlacementLockJournal::$guardSawLockedArea = $location->relationLoaded('area');

        return null;
    }
}

beforeEach(function (): void {
    PlacementLockJournal::$events = [];
    PlacementLockJournal::$guardInTransaction = false;
    PlacementLockJournal::$guardSawLockedArea = false;
});

/**
 * @return array{0: Warehouse, 1: Area, 2: Location}
 */
function lockingTarget(?float $locationMaxKg = null, ?float $areaMaxKg = null): array
{
    $warehouse = Warehouse::factory()->createOne();
    $area = Area::factory()->createOne(['warehouse_id' => $warehouse->id, 'max_weight_kg' => $areaMaxKg]);
    $location = Location::factory()->createOne(['area_id' => $area->id, 'max_weight_kg' => $locationMaxKg]);

    return [$warehouse, $area, $location];
}

function lockingUnit(Warehouse $warehouse, float $kg): StockUnit
{
    return app(CreateStockUnitAction::class)->execute(new StockUnitData(
        warehouseId: $warehouse->id,
        containerType: ContainerTypeEnum::BOX,
        netWeightKg: $kg,
    ));
}

test('put-away locks area and location before running the guards, inside the transaction', function (): void {
    app()->instance(LockPlacementTargetAction::class, new RecordingLockPlacementTargetAction);
    config()->push('warehouse.placement_guards', RecordingPlacementGuard::class);

    [$warehouse, , $location] = lockingTarget();
    $unit = lockingUnit($warehouse, 10.0);

    app(PutAwayStockUnitAction::class)->execute($unit, $location);

    Assert::assertSame(['lock_target', 'guard'], PlacementLockJournal::$events);
    Assert::assertTrue(PlacementLockJournal::$guardInTransaction);
    Assert::assertTrue(PlacementLockJournal::$guardSawLockedArea);
});

test('the lock action returns the fresh location with its area already set', function (): void {
    [, $area, $location] = lockingTarget();

    $locked = DB::connection('warehouse')->transaction(
        static fn (): Location => app(LockPlacementTargetAction::class)->execute($location),
    );

    Assert::assertNotSame($location, $locked);
    Assert::assertSame($location->id, $locked->id);
    Assert::assertTrue($locked->relationLoaded('area'));
    Assert::assertSame($area->id, $locked->area->id);
});

test('a second unit that would exceed the location capacity is denied', function (): void {
    [$warehouse, , $location] = lockingTarget(locationMaxKg: 100.0);
    $first = lockingUnit($warehouse, 60.0);
    $second = lockingUnit($warehouse, 60.0);

    app(PutAwayStockUnitAction::class)->execute($first, $location);

    app(PutAwayStockUnitAction::class)->execute($second, $location);
})->throws(PlacementDeniedException::class);

test('a second unit that would exceed the area capacity is denied', function (): void {
    [$warehouse, $area, $location] = lockingTarget(areaMaxKg: 100.0);
    $other = Location::factory()->createOne(['area_id' => $area->id]);
    $first = lockingUnit($warehouse, 60.0);
    $second = lockingUnit($warehouse, 60.0);

    app(PutAwayStockUnitAction::class)->execute($first, $location);

    app(PutAwayStockUnitAction::class)->execute($second, $other);
})->throws(PlacementDeniedException::class);

test('locking and plain load reads return the same kilograms', function (): void {
    [$warehouse, $area, $location] = lockingTarget();
    app(PutAwayStockUnitAction::class)->execute(lockingUnit($warehouse, 12.345), $location);
    app(PutAwayStockUnitAction::class)->execute(lockingUnit($warehouse, 7.655), $location);

    Assert::assertSame(20.0, app(GetLocationLoadKgAction::class)->execute($location));
    Assert::assertSame(20.0, app(GetLocationLoadKgAction::class)->execute($location, lockingRead: true));
    Assert::assertSame(20.0, app(GetAreaLoadKgAction::class)->execute($area));
    Assert::assertSame(20.0, app(GetAreaLoadKgAction::class)->execute($area, lockingRead: true));
});
