<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Carico attuale di una posizione: somma di `net_weight_kg` delle unità pending/stored/reserved presenti.
<<<<<<< HEAD
=======
 *
 * `$lockingRead = true` (guard di posizionamento): lettura con lock condiviso, che in MySQL legge sempre
 * l'ultima versione confermata e non lo snapshot della transazione (REPEATABLE READ): SumNetWeightKgAction.
>>>>>>> laraxot/dev
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
<<<<<<< HEAD
=======
    public function __construct(
        private readonly SumNetWeightKgAction $sumNetWeight,
    ) {}

    public function execute(Location $location, bool $lockingRead = false): float
    {
        $query = StockUnit::query()
            ->occupying()
            ->where('location_id', $location->id);

        return $this->sumNetWeight->execute($query, $lockingRead);
>>>>>>> laraxot/dev
    }
}
