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
 */
interface PlacementGuardContract
{
    /** Ritorna null se consentito, altrimenti il motivo (testo già tradotto) del rifiuto. */
    public function deny(StockUnit $unit, Location $location): ?string;
}
