<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Carico attuale di un'area: somma di `net_weight_kg` delle unità pending/stored/reserved
 * presenti nelle sue posizioni (anche posizioni disattivate o eliminate, se ospitano ancora unità).
 */
class GetAreaLoadKgAction
{
    use QueueableAction;

    public function execute(Area $area): float
    {
        $sum = StockUnit::query()
            ->occupying()
            ->whereIn('location_id', Location::withTrashed()->select('id')->where('area_id', $area->id))
            ->sum('net_weight_kg');

        return round(SafeFloatCastAction::cast($sum), 3);
    }
}
