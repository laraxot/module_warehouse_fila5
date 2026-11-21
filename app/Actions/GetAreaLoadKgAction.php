<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Carico attuale di un'area: somma di `net_weight_kg` delle unità pending/stored/reserved
 * presenti nelle sue posizioni (anche posizioni disattivate o eliminate, se ospitano ancora unità).
 *
 * Se il modello arriva da una query con `withLoadKg()` usa il valore già caricato (niente query per riga),
 * salvo `$lockingRead`, che rilegge sempre dal database.
 *
 * `$lockingRead = true` (guard di posizionamento): vedi GetLocationLoadKgAction.
 */
class GetAreaLoadKgAction
{
    use QueueableAction;

    public function __construct(
        private readonly SumNetWeightKgAction $sumNetWeight,
    ) {}

    public function execute(Area $area, bool $lockingRead = false): float
    {
        if (! $lockingRead && ($preloaded = $area->preloadedLoadKg()) !== null) {
            return $preloaded;
        }

        $query = StockUnit::query()
            ->occupying()
            ->whereIn('location_id', Location::withTrashed()->select('id')->where('area_id', $area->id));

        return $this->sumNetWeight->execute($query, $lockingRead);
    }
}
