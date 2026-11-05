<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Somma `net_weight_kg` delle unità selezionate da `$query`, arrotondata ai grammi.
 *
 * `$lockingRead = true`: lettura con lock condiviso (`FOR SHARE` / `LOCK IN SHARE MODE`), che in MySQL
 * legge l'ultima versione confermata invece dello snapshot della transazione (REPEATABLE READ) e tiene
 * ferme le righe lette fino al commit. Serve ai guard di posizionamento, dopo il lock di area e posizione
 * (LockPlacementTargetAction). Le righe sono sommate in PHP: PostgreSQL non ammette `FOR SHARE` con le
 * funzioni di aggregazione. SQLite ignora i lock.
 */
class SumNetWeightKgAction
{
    use QueueableAction;

    /**
     * @param  Builder<StockUnit>  $query
     */
    public function execute(Builder $query, bool $lockingRead = false): float
    {
        if (! $lockingRead) {
            return round(SafeFloatCastAction::cast($query->sum('net_weight_kg')), 3);
        }

        $total = 0.0;
        foreach ($query->sharedLock()->pluck('net_weight_kg') as $kg) {
            $total += SafeFloatCastAction::cast($kg);
        }

        return round($total, 3);
    }
}
