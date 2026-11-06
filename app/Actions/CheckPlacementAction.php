<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Contracts\PlacementGuardContract;
use Modules\Warehouse\Exceptions\InvalidPlacementGuardException;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Consulta in ordine i guard di `config('warehouse.placement_guards')` e restituisce il primo
 * motivo di rifiuto (già tradotto), oppure null se il posizionamento è consentito.
 */
class CheckPlacementAction
{
    use QueueableAction;

    public function execute(StockUnit $unit, Location $location): ?string
    {
        foreach ($this->guards() as $guard) {
            $reason = $guard->deny($unit, $location);

            if ($reason !== null) {
                return $reason;
            }
        }

        return null;
    }

    /**
     * @return list<PlacementGuardContract>
     */
    public function guards(): array
    {
        $configured = config('warehouse.placement_guards', []);
        $entries = \is_array($configured) ? array_values(array_unique($configured, SORT_REGULAR)) : [];

        $guards = [];
        foreach ($entries as $entry) {
            if (! \is_string($entry) || ! is_a($entry, PlacementGuardContract::class, true)) {
                throw InvalidPlacementGuardException::forEntry($entry);
            }

            $guard = app($entry);
            if (! $guard instanceof PlacementGuardContract) {
                throw InvalidPlacementGuardException::forEntry($entry);
            }

            $guards[] = $guard;
        }

        return $guards;
    }
}
