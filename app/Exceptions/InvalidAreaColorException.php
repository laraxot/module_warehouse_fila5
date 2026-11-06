<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Colore dell'area non nel formato `#rrggbb`: finisce nell'attributo `style` della mappa,
 * quindi si accetta solo un esadecimale (audit sicurezza B3, iniezione CSS).
 */
class InvalidAreaColorException extends WarehouseException
{
    public static function forValue(string $value): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.area.invalid_color', ['color' => mb_substr($value, 0, 32)])));
    }
}
