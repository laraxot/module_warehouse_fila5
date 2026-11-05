<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Tentativo di modificare o eliminare un movimento di magazzino (registro solo insert).
 */
class ImmutableStockMovementException extends WarehouseException
{
    public static function forUpdate(int $movementId): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.immutable_movement.update', ['id' => $movementId])));
    }

    public static function forDelete(int $movementId): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.immutable_movement.delete', ['id' => $movementId])));
    }
}
