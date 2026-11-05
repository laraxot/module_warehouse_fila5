<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use LogicException;
use Modules\Warehouse\Contracts\PlacementGuardContract;

/**
 * Errore di configurazione: una voce di `warehouse.placement_guards` non è un PlacementGuardContract.
 * Non estende WarehouseException perché è un errore dello sviluppatore, non dell'operatore.
 */
class InvalidPlacementGuardException extends LogicException
{
    public static function forEntry(mixed $entry): self
    {
        return new self(sprintf(
            'warehouse.placement_guards: [%s] non implementa %s.',
            \is_string($entry) ? $entry : get_debug_type($entry),
            PlacementGuardContract::class,
        ));
    }
}
