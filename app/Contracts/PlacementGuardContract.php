<?php

declare(strict_types=1);

namespace Modules\Warehouse\Contracts;

use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;

/**
 * Punto di estensione: regola che può vietare il posizionamento di una unità in una posizione.
 *
 * I guard sono registrati in `config('warehouse.placement_guards')` (array di class-string,
 * risolti dal container) e consultati in ordine da CheckPlacementAction: il primo motivo
 * non nullo blocca il posizionamento.
 *
 * Un guard viene chiamato anche per unità non ancora salvate (creazione con posizione):
 * non deve dare per scontato `$unit->exists`.
<<<<<<< HEAD
=======
 *
 * Concorrenza: dentro PutAwayStockUnitAction i guard girano nella transazione della connessione
 * `warehouse`, dopo il lock (FOR UPDATE) dell'area e della posizione di destinazione e dell'unità
 * (LockPlacementTargetAction, ordine area → posizione → unità). `$location` è la riga bloccata, con
 * la relazione `area` bloccata già caricata. Un guard che legge carichi deve usare letture bloccanti
 * (es. GetAreaLoadKgAction con `lockingRead: true`); se blocca righe proprie (anche su altre
 * connessioni) lo fa qui dentro, sempre dopo i lock di Warehouse e sempre nello stesso ordine.
 * Lo stesso guard può essere chiamato anche fuori transazione (controllo preventivo per un
 * messaggio all'operatore, es. CheckPlacementAction da Roosko): l'esito vincolante è quello
 * dentro PutAwayStockUnitAction.
>>>>>>> laraxot/dev
 */
interface PlacementGuardContract
{
    /** Ritorna null se consentito, altrimenti il motivo (testo già tradotto) del rifiuto. */
    public function deny(StockUnit $unit, Location $location): ?string;
}
