<?php

declare(strict_types=1);

use Modules\Warehouse\Actions\CheckPlacementAction;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Actions\PutAwayStockUnitAction;
use Modules\Warehouse\Contracts\PlacementGuardContract;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Exceptions\InvalidPlacementGuardException;
use Modules\Warehouse\Exceptions\PlacementDeniedException;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/** Guard di prova: vieta le posizioni il cui codice finisce con "-X". */
final class NoXLocationGuard implements PlacementGuardContract
{
    public function deny(StockUnit $unit, Location $location): ?string
    {
        return str_ends_with($location->code, '-X') ? 'posizione X vietata' : null;
    }
}

test('the module registers the capacity guard by default', function (): void {
    $guards = app(CheckPlacementAction::class)->guards();

    Assert::assertNotEmpty($guards);
});

test('a guard pushed by another module is consulted', function (): void {
    config()->push('warehouse.placement_guards', NoXLocationGuard::class);

    $warehouse = Warehouse::factory()->createOne();
    $area = Area::factory()->createOne(['warehouse_id' => $warehouse->id]);
    $forbidden = Location::factory()->createOne(['area_id' => $area->id, 'code' => 'ST-X']);
    $allowed = Location::factory()->createOne(['area_id' => $area->id, 'code' => 'ST-Y']);
    $unit = app(CreateStockUnitAction::class)->execute(new StockUnitData(
        warehouseId: $warehouse->id,
        containerType: ContainerTypeEnum::BOX,
        netWeightKg: 10.0,
    ));

    Assert::assertSame('posizione X vietata', app(CheckPlacementAction::class)->execute($unit, $forbidden));

    try {
        app(PutAwayStockUnitAction::class)->execute($unit, $forbidden);
        Assert::fail('PlacementDeniedException expected');
    } catch (PlacementDeniedException $e) {
        Assert::assertSame('posizione X vietata', $e->getMessage());
    }

    app(PutAwayStockUnitAction::class)->execute($unit, $allowed);
    Assert::assertSame(StockUnitStatusEnum::STORED, $unit->status);
});

test('a misconfigured guard fails loudly', function (): void {
    config()->push('warehouse.placement_guards', stdClass::class);

    app(CheckPlacementAction::class)->guards();
})->throws(InvalidPlacementGuardException::class);
