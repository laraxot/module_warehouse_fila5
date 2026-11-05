<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Spatie\QueueableAction\QueueableAction;

/**
 * Blocca (`SELECT ... FOR UPDATE`) l'area e poi la posizione di destinazione di un posizionamento e
 * restituisce la posizione riletta, con l'area bloccata già impostata come relazione.
 *
 * Va chiamata dentro la transazione della connessione `warehouse`, prima di bloccare l'unità e prima
 * dei guard (PutAwayStockUnitAction). Due posizionamenti verso la stessa area o la stessa posizione si
 * mettono in coda: il secondo legge i carichi solo dopo il commit del primo.
 *
 * Ordine dei lock, uguale in tutte le action del modulo: area → posizione → unità. Le action che
 * bloccano solo l'unità (rettifica, annullo, uscita, consumo) rispettano l'ordine perché prendono
 * soltanto l'ultimo anello. Un guard di un altro modulo che blocca righe proprie lo fa dopo
 * (dentro `deny()`), mai prima di chiamare Warehouse.
 */
class LockPlacementTargetAction
{
    use QueueableAction;

    public function execute(Location $location): Location
    {
        $area = Area::withTrashed()
            ->whereKey($location->area_id)
            ->lockForUpdate()
            ->firstOrFail();

        $locked = Location::query()
            ->whereKey($location->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        $locked->setRelation('area', $area);

        return $locked;
    }
}
