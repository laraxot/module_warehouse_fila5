<?php

declare(strict_types=1);

use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Enums\OccupancyLevelEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('StockUnitStatusEnum', function (): void {
    test('final states have no transitions', function (): void {
        foreach ([StockUnitStatusEnum::CONSUMED, StockUnitStatusEnum::SHIPPED, StockUnitStatusEnum::VOIDED] as $final) {
            Assert::assertSame([], $final->allowedTransitions());
            Assert::assertFalse($final->isInStock());
        }
    });

    test('in process units cannot be shipped without being stored first', function (): void {
        Assert::assertFalse(StockUnitStatusEnum::IN_PROCESS->canTransitionTo(StockUnitStatusEnum::SHIPPED));
        Assert::assertTrue(StockUnitStatusEnum::IN_PROCESS->canTransitionTo(StockUnitStatusEnum::STORED));
    });

    test('occupying states are pending, stored and reserved', function (): void {
        Assert::assertSame(
            [StockUnitStatusEnum::PENDING, StockUnitStatusEnum::STORED, StockUnitStatusEnum::RESERVED],
            StockUnitStatusEnum::occupying(),
        );
    });
});

describe('MovementTypeEnum::weightAfter', function (): void {
    test('origin adds, consumption subtracts, placement keeps, exits zero', function (): void {
        Assert::assertSame(500.0, MovementTypeEnum::RECEIPT->weightAfter(0.0, 500.0));
        Assert::assertSame(350.0, MovementTypeEnum::CONSUMPTION->weightAfter(500.0, 150.0));
        Assert::assertSame(500.0, MovementTypeEnum::TRANSFER->weightAfter(500.0, 500.0));
        Assert::assertSame(0.0, MovementTypeEnum::SHIPMENT->weightAfter(500.0, 500.0));
    });

    test('adjustment applies a signed delta', function (): void {
        Assert::assertSame(480.0, MovementTypeEnum::ADJUSTMENT->weightAfter(500.0, -20.0));
        Assert::assertSame(512.5, MovementTypeEnum::ADJUSTMENT->weightAfter(500.0, 12.5));
    });
});

describe('OccupancyLevelEnum::fromPercentage', function (): void {
    test('maps percentages to levels', function (): void {
        Assert::assertSame(OccupancyLevelEnum::UNBOUNDED, OccupancyLevelEnum::fromPercentage(null));
        Assert::assertSame(OccupancyLevelEnum::EMPTY, OccupancyLevelEnum::fromPercentage(0.0));
        Assert::assertSame(OccupancyLevelEnum::LOW, OccupancyLevelEnum::fromPercentage(49.9));
        Assert::assertSame(OccupancyLevelEnum::MEDIUM, OccupancyLevelEnum::fromPercentage(50.0));
        Assert::assertSame(OccupancyLevelEnum::HIGH, OccupancyLevelEnum::fromPercentage(99.9));
        Assert::assertSame(OccupancyLevelEnum::FULL, OccupancyLevelEnum::fromPercentage(100.0));
    });
});
