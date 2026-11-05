<?php

declare(strict_types=1);

use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Exceptions\ImmutableStockMovementException;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

function firstMovement(): StockMovement
{
    $warehouse = Warehouse::factory()->createOne();
    $unit = app(CreateStockUnitAction::class)->execute(new StockUnitData(
        warehouseId: $warehouse->id,
        containerType: ContainerTypeEnum::DRUM,
        netWeightKg: 180.0,
    ));

    return $unit->movements()->firstOrFail();
}

test('a movement cannot be updated', function (): void {
    $movement = firstMovement();

    $movement->notes = 'manomesso';
    $movement->save();
})->throws(ImmutableStockMovementException::class);

test('a movement cannot be deleted', function (): void {
    $movement = firstMovement();

    $movement->delete();
})->throws(ImmutableStockMovementException::class);

test('the stored movement is unchanged after a rejected update', function (): void {
    $movement = firstMovement();

    try {
        $movement->update(['quantity_kg' => 1]);
    } catch (ImmutableStockMovementException) {
    }

    Assert::assertSame(180.0, StockMovement::query()->findOrFail($movement->id)->quantityKg());
});
