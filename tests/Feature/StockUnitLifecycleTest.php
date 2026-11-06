<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Actions\AdjustStockUnitWeightAction;
use Modules\Warehouse\Actions\ConsumeStockUnitAction;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Actions\GetAreaLoadKgAction;
use Modules\Warehouse\Actions\GetLocationLoadKgAction;
use Modules\Warehouse\Actions\PutAwayStockUnitAction;
use Modules\Warehouse\Actions\ShipStockUnitAction;
use Modules\Warehouse\Actions\VoidStockUnitAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Exceptions\InvalidStockQuantityException;
use Modules\Warehouse\Exceptions\LocationMismatchException;
use Modules\Warehouse\Exceptions\PlacementDeniedException;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * Magazzino con un'area da 2000 kg e due posizioni da 1000 kg.
 *
 * @return array{warehouse: Warehouse, area: Area, a: Location, b: Location}
 */
function warehouseFixture(): array
{
    $warehouse = Warehouse::factory()->createOne();
    $area = Area::factory()->withMaxWeight(2000.0)->createOne(['warehouse_id' => $warehouse->id, 'code' => 'ST']);
    $a = Location::factory()->withMaxWeight(1000.0)->createOne(['area_id' => $area->id, 'code' => 'ST-01']);
    $b = Location::factory()->withMaxWeight(1000.0)->createOne(['area_id' => $area->id, 'code' => 'ST-02']);

    return ['warehouse' => $warehouse, 'area' => $area, 'a' => $a, 'b' => $b];
}

function createUnit(Warehouse $warehouse, float $kg, ?Location $location = null, MovementTypeEnum $origin = MovementTypeEnum::RECEIPT, ?Model $reference = null): StockUnit
{
    return app(CreateStockUnitAction::class)->execute(
        new StockUnitData(
            warehouseId: $warehouse->id,
            containerType: ContainerTypeEnum::BIG_BAG,
            netWeightKg: $kg,
            tareWeightKg: 4.5,
            locationId: $location?->id,
        ),
        $origin,
        $reference,
    );
}

/**
 * @return list<MovementTypeEnum>
 */
function movementTypes(StockUnit $unit): array
{
    $types = [];
    foreach ($unit->movements()->get() as $movement) {
        $types[] = $movement->type;
    }

    return $types;
}

describe('create', function (): void {
    test('creates a pending unit with a SU code and a receipt movement', function (): void {
        ['warehouse' => $warehouse] = warehouseFixture();

        $unit = createUnit($warehouse, 750.0);

        Assert::assertMatchesRegularExpression('/^SU-'.now()->year.'-\d{6}$/', $unit->code);
        Assert::assertSame(StockUnitStatusEnum::PENDING, $unit->status);
        Assert::assertNull($unit->location_id);
        Assert::assertSame(750.0, $unit->netWeightKg());

        $movements = $unit->movements()->get();
        Assert::assertCount(1, $movements);
        $receipt = $movements->firstOrFail();
        Assert::assertSame(MovementTypeEnum::RECEIPT, $receipt->type);
        Assert::assertSame(750.0, $receipt->quantityKg());
        Assert::assertSame(0.0, (float) $receipt->weight_before_kg);
        Assert::assertSame(750.0, (float) $receipt->weight_after_kg);
    });

    test('codes are sequential', function (): void {
        ['warehouse' => $warehouse] = warehouseFixture();

        $first = createUnit($warehouse, 100.0);
        $second = createUnit($warehouse, 100.0);

        $n1 = (int) substr($first->code, -6);
        $n2 = (int) substr($second->code, -6);
        Assert::assertSame($n1 + 1, $n2);
    });

    test('production origin with a reference is recorded on the movement', function (): void {
        ['warehouse' => $warehouse] = warehouseFixture();
        $reference = Warehouse::factory()->createOne();

        $unit = createUnit($warehouse, 300.0, null, MovementTypeEnum::PRODUCTION, $reference);

        $movement = $unit->movements()->firstOrFail();
        Assert::assertSame(MovementTypeEnum::PRODUCTION, $movement->type);
        Assert::assertSame($reference->getMorphClass(), $movement->reference_type);
        Assert::assertSame($reference->id, $movement->reference_id);
    });

    test('with a location it is put away after the receipt', function (): void {
        ['warehouse' => $warehouse, 'a' => $a] = warehouseFixture();

        $unit = createUnit($warehouse, 400.0, $a);

        Assert::assertSame(StockUnitStatusEnum::STORED, $unit->status);
        Assert::assertSame($a->id, $unit->location_id);
        Assert::assertSame([MovementTypeEnum::RECEIPT, MovementTypeEnum::PUTAWAY], movementTypes($unit));
        Assert::assertSame(400.0, app(GetLocationLoadKgAction::class)->execute($a));
    });

    test('a denied placement rolls back the whole creation', function (): void {
        ['warehouse' => $warehouse, 'a' => $a] = warehouseFixture();
        $before = StockUnit::withTrashed()->count();

        try {
            createUnit($warehouse, 1500.0, $a);
            Assert::fail('PlacementDeniedException expected');
        } catch (PlacementDeniedException $e) {
            Assert::assertNotSame('', $e->getMessage());
        }

        Assert::assertSame($before, StockUnit::withTrashed()->count());
    });

    test('rejects a non positive weight', function (): void {
        ['warehouse' => $warehouse] = warehouseFixture();

        createUnit($warehouse, 0.0);
    })->throws(InvalidStockQuantityException::class);
});

describe('putaway and transfer', function (): void {
    test('putaway then transfer records from/to and keeps the weight', function (): void {
        ['warehouse' => $warehouse, 'a' => $a, 'b' => $b] = warehouseFixture();
        $unit = createUnit($warehouse, 600.0);

        app(PutAwayStockUnitAction::class)->execute($unit, $a);
        app(PutAwayStockUnitAction::class)->execute($unit, $b);

        Assert::assertSame($b->id, $unit->location_id);
        Assert::assertSame(StockUnitStatusEnum::STORED, $unit->status);
        Assert::assertSame([MovementTypeEnum::RECEIPT, MovementTypeEnum::PUTAWAY, MovementTypeEnum::TRANSFER], movementTypes($unit));

        $transfer = $unit->movements()->orderByDesc('id')->firstOrFail();
        Assert::assertSame($a->id, $transfer->from_location_id);
        Assert::assertSame($b->id, $transfer->to_location_id);
        Assert::assertSame(600.0, (float) $transfer->weight_after_kg);

        Assert::assertSame(0.0, app(GetLocationLoadKgAction::class)->execute($a));
        Assert::assertSame(600.0, app(GetLocationLoadKgAction::class)->execute($b));
    });

    test('moving inside a full area does not count the unit twice', function (): void {
        ['warehouse' => $warehouse, 'area' => $area, 'a' => $a, 'b' => $b] = warehouseFixture();
        createUnit($warehouse, 1000.0, $b);
        $unit = createUnit($warehouse, 1000.0, $a);
        Assert::assertSame(2000.0, app(GetAreaLoadKgAction::class)->execute($area));

        $c = Location::factory()->withMaxWeight(1000.0)->createOne(['area_id' => $area->id, 'code' => 'ST-03']);
        app(PutAwayStockUnitAction::class)->execute($unit, $c);

        Assert::assertSame($c->id, $unit->location_id);
    });

    test('a location of another warehouse is rejected', function (): void {
        ['warehouse' => $warehouse] = warehouseFixture();
        ['a' => $foreign] = warehouseFixture();
        $unit = createUnit($warehouse, 100.0);

        app(PutAwayStockUnitAction::class)->execute($unit, $foreign);
    })->throws(LocationMismatchException::class);

    test('the area capacity is enforced across locations', function (): void {
        ['warehouse' => $warehouse, 'area' => $area, 'a' => $a, 'b' => $b] = warehouseFixture();
        createUnit($warehouse, 1000.0, $a);
        createUnit($warehouse, 900.0, $b);
        $c = Location::factory()->createOne(['area_id' => $area->id, 'code' => 'ST-09']);

        $unit = createUnit($warehouse, 200.0);

        app(PutAwayStockUnitAction::class)->execute($unit, $c);
    })->throws(PlacementDeniedException::class);
});

describe('consume', function (): void {
    test('partial consumption lowers the weight and keeps the location', function (): void {
        ['warehouse' => $warehouse, 'a' => $a] = warehouseFixture();
        $unit = createUnit($warehouse, 500.0, $a);

        $movement = app(ConsumeStockUnitAction::class)->execute($unit, 200.0);

        Assert::assertSame(MovementTypeEnum::CONSUMPTION, $movement->type);
        Assert::assertSame(500.0, (float) $movement->weight_before_kg);
        Assert::assertSame(300.0, (float) $movement->weight_after_kg);
        Assert::assertSame(300.0, $unit->netWeightKg());
        Assert::assertSame(StockUnitStatusEnum::STORED, $unit->status);
        Assert::assertSame($a->id, $unit->location_id);
    });

    test('total consumption marks the unit consumed and frees the location', function (): void {
        ['warehouse' => $warehouse, 'a' => $a] = warehouseFixture();
        $unit = createUnit($warehouse, 500.0, $a);

        app(ConsumeStockUnitAction::class)->execute($unit, 500.0);

        Assert::assertSame(StockUnitStatusEnum::CONSUMED, $unit->status);
        Assert::assertNull($unit->location_id);
        Assert::assertSame(0.0, $unit->netWeightKg());
        Assert::assertSame(0.0, app(GetLocationLoadKgAction::class)->execute($a));
    });

    test('cannot consume more than the stock', function (): void {
        ['warehouse' => $warehouse] = warehouseFixture();
        $unit = createUnit($warehouse, 100.0);

        app(ConsumeStockUnitAction::class)->execute($unit, 100.5);
    })->throws(InvalidStockQuantityException::class);
});

describe('ship', function (): void {
    test('shipping records the movement, frees the location and blocks further moves', function (): void {
        ['warehouse' => $warehouse, 'a' => $a, 'b' => $b] = warehouseFixture();
        $unit = createUnit($warehouse, 800.0, $a);

        $movement = app(ShipStockUnitAction::class)->execute($unit);

        Assert::assertSame(MovementTypeEnum::SHIPMENT, $movement->type);
        Assert::assertSame(800.0, $movement->quantityKg());
        Assert::assertSame($a->id, $movement->from_location_id);
        Assert::assertSame(0.0, (float) $movement->weight_after_kg);
        Assert::assertSame(StockUnitStatusEnum::SHIPPED, $unit->status);
        Assert::assertNull($unit->location_id);
        Assert::assertSame(800.0, $unit->netWeightKg(), 'il peso spedito resta sull\'unità');

        $this->expectException(InvalidArgumentException::class);
        app(PutAwayStockUnitAction::class)->execute($unit, $b);
    });
});

describe('adjust and void', function (): void {
    test('adjustment records the signed delta and the reason', function (): void {
        ['warehouse' => $warehouse, 'a' => $a] = warehouseFixture();
        $unit = createUnit($warehouse, 500.0, $a);

        $movement = app(AdjustStockUnitWeightAction::class)->execute($unit, 487.25, 'ripesata');

        Assert::assertSame(MovementTypeEnum::ADJUSTMENT, $movement->type);
        Assert::assertSame(-12.75, $movement->quantityKg());
        Assert::assertSame('ripesata', $movement->notes);
        Assert::assertSame(487.25, $unit->netWeightKg());
    });

    test('adjustment requires a reason', function (): void {
        ['warehouse' => $warehouse] = warehouseFixture();
        $unit = createUnit($warehouse, 500.0);

        app(AdjustStockUnitWeightAction::class)->execute($unit, 400.0, '  ');
    })->throws(InvalidArgumentException::class);

    test('void frees the location and is final', function (): void {
        ['warehouse' => $warehouse, 'a' => $a] = warehouseFixture();
        $unit = createUnit($warehouse, 500.0, $a);

        $movement = app(VoidStockUnitAction::class)->execute($unit, 'registrata per errore');

        Assert::assertSame(MovementTypeEnum::VOID, $movement->type);
        Assert::assertSame(StockUnitStatusEnum::VOIDED, $unit->status);
        Assert::assertNull($unit->location_id);
        Assert::assertFalse($unit->status->canTransitionTo(StockUnitStatusEnum::STORED));
    });
});
