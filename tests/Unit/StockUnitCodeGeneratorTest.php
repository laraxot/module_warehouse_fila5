<?php

declare(strict_types=1);

use Modules\Warehouse\Actions\GenerateStockUnitCodeAction;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('GenerateStockUnitCodeAction pure helpers', function (): void {
    test('formats SU-{year}-{6 digits}', function (): void {
        Assert::assertSame('SU-2026-000001', GenerateStockUnitCodeAction::format(2026, 1));
        Assert::assertSame('SU-2026-123456', GenerateStockUnitCodeAction::format(2026, 123456));
    });

    test('rejects numbers that do not fit 6 digits', function (): void {
        GenerateStockUnitCodeAction::format(2026, 1_000_000);
    })->throws(InvalidArgumentException::class);

    test('first code of the year starts at 1', function (): void {
        Assert::assertSame(1, GenerateStockUnitCodeAction::nextNumber(null, 2026));
    });

    test('next number follows the last code of the same year', function (): void {
        Assert::assertSame(42, GenerateStockUnitCodeAction::nextNumber('SU-2026-000041', 2026));
    });

    test('a code of another year restarts the sequence', function (): void {
        Assert::assertSame(1, GenerateStockUnitCodeAction::nextNumber('SU-2025-000999', 2026));
    });

    test('a malformed last code is an error, not a silent restart', function (): void {
        GenerateStockUnitCodeAction::nextNumber('SU-2026-00A001', 2026);
    })->throws(InvalidArgumentException::class);
});
