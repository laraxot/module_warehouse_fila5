<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Carico attuale di una posizione: somma di `net_weight_kg` delle unità pending/stored/reserved presenti.
 */
class GetLocationLoadKgAction
{
    use QueueableAction;

    public function execute(Location $location): float
    {
        $sum = StockUnit::query()
            ->occupying()
            ->where('location_id', $location->id)
            ->sum('net_weight_kg');

        return round(SafeFloatCastAction::cast($sum), 3);
    }
}
