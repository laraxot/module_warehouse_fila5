<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;

/**
 * Carico attuale di una posizione: somma di `net_weight_kg` delle unità pending/stored/reserved presenti.
 *
 * Se il modello arriva da una query con `withLoadKg()` usa il valore già caricato (niente query per riga),
 * salvo `$lockingRead`, che rilegge sempre dal database.
 *
 * `$lockingRead = true` (guard di posizionamento): lettura con lock condiviso, che in MySQL legge sempre
 * l'ultima versione confermata e non lo snapshot della transazione (REPEATABLE READ): SumNetWeightKgAction.
 */
class GetLocationLoadKgAction
{
    use QueueableAction;

    public function __construct(
        private readonly SumNetWeightKgAction $sumNetWeight,
    ) {}

    public function execute(Location $location, bool $lockingRead = false): float
    {
        if (! $lockingRead && ($preloaded = $location->preloadedLoadKg()) !== null) {
            return $preloaded;
        }

        $query = StockUnit::query()
            ->occupying()
            ->where('location_id', $location->id);

        return $this->sumNetWeight->execute($query, $lockingRead);
    }
}
