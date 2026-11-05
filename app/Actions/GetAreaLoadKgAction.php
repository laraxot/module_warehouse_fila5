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
<<<<<<< HEAD
=======
 *
 * `$lockingRead = true` (guard di posizionamento): vedi GetLocationLoadKgAction.
 * Per molte aree insieme (tabelle, widget) usare lo scope `Area::withLoadKg()`: una query sola.
>>>>>>> laraxot/dev
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
<<<<<<< HEAD
=======
    public function __construct(
        private readonly SumNetWeightKgAction $sumNetWeight,
    ) {}

    public function execute(Area $area, bool $lockingRead = false): float
    {
        $query = StockUnit::query()
            ->occupying()
            ->whereIn('location_id', Location::withTrashed()->select('id')->where('area_id', $area->id));

        return $this->sumNetWeight->execute($query, $lockingRead);
>>>>>>> laraxot/dev
    }
}
