<?php

declare(strict_types=1);

return [
    'label' => 'Tipo contenitore',
    'values' => [
        'big_bag' => [
            'label' => 'Big bag',
            'color' => 'primary',
            'icon' => 'heroicon-o-shopping-bag',
            'description' => 'Sacco flessibile (FIBC)',
        ],
        'drum' => [
            'label' => 'Fusto',
            'color' => 'info',
            'icon' => 'heroicon-o-circle-stack',
            'description' => 'Fusto metallico o in plastica',
        ],
        'ibc' => [
            'label' => 'Cisternetta IBC',
            'color' => 'info',
            'icon' => 'heroicon-o-cube-transparent',
            'description' => 'Contenitore intermedio rigido per liquidi',
        ],
        'skip' => [
            'label' => 'Cassone scarrabile',
            'color' => 'warning',
            'icon' => 'heroicon-o-archive-box',
            'description' => 'Cassone a caricamento dall\'alto',
        ],
        'roll_off' => [
            'label' => 'Container roll-off',
            'color' => 'warning',
            'icon' => 'heroicon-o-truck',
            'description' => 'Container scarrabile a gancio',
        ],
        'pallet' => [
            'label' => 'Pallet',
            'color' => 'gray',
            'icon' => 'heroicon-o-rectangle-stack',
            'description' => 'Merce su bancale',
        ],
        'bulk_pile' => [
            'label' => 'Cumulo sfuso',
            'color' => 'gray',
            'icon' => 'heroicon-o-chart-pie',
            'description' => 'Materiale sfuso a cumulo',
        ],
        'tank' => [
            'label' => 'Serbatoio',
            'color' => 'danger',
            'icon' => 'heroicon-o-beaker',
            'description' => 'Serbatoio fisso o mobile',
        ],
        'box' => [
            'label' => 'Cassa / scatola',
            'color' => 'gray',
            'icon' => 'heroicon-o-archive-box-arrow-down',
            'description' => 'Cassa, scatola o contenitore rigido',
        ],
        'other' => [
            'label' => 'Altro',
            'color' => 'gray',
            'icon' => 'heroicon-o-question-mark-circle',
            'description' => 'Altro contenitore',
        ],
    ],
];
