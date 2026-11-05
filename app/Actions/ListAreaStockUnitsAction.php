<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Unità che occupano un'area (le stesse che compongono GetAreaLoadKgAction), con la posizione.
 */
class ListAreaStockUnitsAction
{
    use QueueableAction;

    /**
     * @return Collection<int, StockUnit>
     */
    public function execute(Area $area): Collection
    {
        return StockUnit::query()
            ->occupying()
            ->whereIn('location_id', Location::withTrashed()->select('id')->where('area_id', $area->id))
            ->with('location')
            ->orderBy('code')
            ->get();
    }
}
