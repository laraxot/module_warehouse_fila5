<?php

declare(strict_types=1);

use Modules\Warehouse\Actions\GetAreaLoadKgAction;
use Modules\Warehouse\Actions\GetLocationLoadKgAction;
use Modules\Warehouse\Datas\CapacityCheckData;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Guards\CapacityPlacementGuard;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * Guard costruito con carichi fissi: nessun accesso al database.
 */
function capacityGuard(float $locationLoadKg, float $areaLoadKg): CapacityPlacementGuard
{
    $locationLoad = Mockery::mock(GetLocationLoadKgAction::class);
    $locationLoad->shouldReceive('execute')->andReturn($locationLoadKg);

    $areaLoad = Mockery::mock(GetAreaLoadKgAction::class);
    $areaLoad->shouldReceive('execute')->andReturn($areaLoadKg);

    return new CapacityPlacementGuard($locationLoad, $areaLoad);
}

function memoryLocation(?float $locationMaxKg, ?float $areaMaxKg, int $id = 10, int $areaId = 1): Location
{
    $warehouse = new Warehouse(['code' => 'WH', 'name' => 'Test']);
    $area = new Area(['code' => 'ST', 'name' => 'Storage', 'max_weight_kg' => $areaMaxKg]);
    $area->id = $areaId;
    $area->setRelation('warehouse', $warehouse);

    $location = new Location(['code' => 'ST-01', 'max_weight_kg' => $locationMaxKg]);
    $location->id = $id;
    $location->area_id = $areaId;
    $location->setRelation('area', $area);

    return $location;
}

function memoryUnit(float $netKg): StockUnit
{
    return new StockUnit(['code' => 'SU-2026-000001', 'net_weight_kg' => $netKg, 'status' => StockUnitStatusEnum::PENDING]);
}

describe('CapacityCheckData', function (): void {
    test('null max means unlimited', function (): void {
        Assert::assertFalse((new CapacityCheckData(null, 1_000_000.0, 1_000.0))->exceeds());
    });

    test('exactly at the limit is allowed', function (): void {
        Assert::assertFalse((new CapacityCheckData(1000.0, 600.0, 400.0))->exceeds());
    });

    test('one gram over the limit is denied', function (): void {
        Assert::assertTrue((new CapacityCheckData(1000.0, 600.0, 400.001))->exceeds());
    });

    test('weight already included in the load is not counted twice', function (): void {
        $check = new CapacityCheckData(1000.0, 900.0, 300.0, alreadyIncludedKg: 300.0);

        Assert::assertSame(900.0, $check->projectedKg());
        Assert::assertFalse($check->exceeds());
    });
});

describe('CapacityPlacementGuard', function (): void {
    test('allows when both location and area have room', function (): void {
        $reason = capacityGuard(200.0, 5_000.0)->deny(memoryUnit(300.0), memoryLocation(1_000.0, 10_000.0));

        Assert::assertNull($reason);
    });

    test('denies when the location max load would be exceeded', function (): void {
        $reason = capacityGuard(800.0, 800.0)->deny(memoryUnit(300.0), memoryLocation(1_000.0, null));

        Assert::assertIsString($reason);
        Assert::assertStringContainsString('ST-01', $reason);
    });

    test('denies when the area max load would be exceeded', function (): void {
        $reason = capacityGuard(0.0, 9_900.0)->deny(memoryUnit(300.0), memoryLocation(null, 10_000.0));

        Assert::assertIsString($reason);
        Assert::assertStringContainsString('ST', $reason);
    });

    test('unlimited location and area always allow', function (): void {
        Assert::assertNull(capacityGuard(1e9, 1e9)->deny(memoryUnit(1e6), memoryLocation(null, null)));
    });
});
