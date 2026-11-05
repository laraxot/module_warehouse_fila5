<?php

declare(strict_types=1);

use Modules\Warehouse\Guards\CapacityPlacementGuard;

return [
    'navigation_sort' => 2,
    'name' => 'Warehouse',
    'description' => 'Magazzino mappato: aree, posizioni, unità di stoccaggio e movimenti',
    'icon' => 'heroicon-o-building-storefront',
    'navigation' => [
        'enabled' => true,
        'sort' => 30,
    ],

    /*
     * Punto di estensione: guard consultati (in ordine) prima di ogni posizionamento.
     * Ogni elemento è una class-string che implementa PlacementGuardContract.
     * Altri moduli aggiungono il proprio guard nel loro ServiceProvider:
     *   config()->push('warehouse.placement_guards', MyPlacementGuard::class);
     */
    'placement_guards' => [
        CapacityPlacementGuard::class,
    ],
];
