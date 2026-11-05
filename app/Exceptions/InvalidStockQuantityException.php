<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Quantità non valida per l'operazione (negativa, nulla o superiore alla giacenza).
 */
class InvalidStockQuantityException extends WarehouseException
{
    public static function notPositive(float $kg): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.invalid_quantity.not_positive', ['kg' => $kg])));
    }

    public static function negative(float $kg): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.invalid_quantity.negative', ['kg' => $kg])));
    }

    public static function unchanged(StockUnit $unit): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.invalid_quantity.unchanged', ['code' => $unit->code])));
    }

    public static function exceedsStock(StockUnit $unit, float $kg): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.invalid_quantity.exceeds_stock', [
            'code' => $unit->code,
            'kg' => $kg,
            'available' => $unit->netWeightKg(),
        ])));
    }
}
