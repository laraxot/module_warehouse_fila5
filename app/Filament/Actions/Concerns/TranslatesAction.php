<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Actions\Concerns;

use Modules\Xot\Actions\GetTransKeyAction;

/**
 * Testi delle Filament actions del modulo dal file lang della action stessa
 * (`lang/{locale}/{nome_action}.php`, es. `move_stock_unit_header.php`), con la stessa
 * chiave che GetTransKeyAction usa per le etichette automatiche dei campi del modale.
 */
trait TranslatesAction
{
    /**
     * @param  array<string, string|int|float>  $replace
     */
    protected static function transAction(string $key, array $replace = []): string
    {
        $prefix = app(GetTransKeyAction::class)->execute(static::class);
        $value = __($prefix.'.'.$key, $replace);

        return \is_string($value) ? $value : $prefix.'.'.$key;
    }
}
