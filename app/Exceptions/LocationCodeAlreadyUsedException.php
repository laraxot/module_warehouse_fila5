<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Il codice posizione è già usato da un'altra posizione dello stesso magazzino.
 */
class LocationCodeAlreadyUsedException extends WarehouseException
{
    public static function forCode(string $code): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.location.code_already_used', ['location' => $code])));
    }
}
