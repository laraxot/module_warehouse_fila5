<?php

declare(strict_types=1);

return [
    'label' => 'Container type',
    'values' => [
        'big_bag' => [
            'label' => 'Big bag',
            'color' => 'primary',
            'icon' => 'heroicon-o-shopping-bag',
            'description' => 'Flexible intermediate bulk container (FIBC)',
        ],
        'drum' => [
            'label' => 'Drum',
            'color' => 'info',
            'icon' => 'heroicon-o-circle-stack',
            'description' => 'Metal or plastic drum',
        ],
        'ibc' => [
            'label' => 'IBC tote',
            'color' => 'info',
            'icon' => 'heroicon-o-cube-transparent',
            'description' => 'Rigid intermediate bulk container for liquids',
        ],
        'skip' => [
            'label' => 'Skip',
            'color' => 'warning',
            'icon' => 'heroicon-o-archive-box',
            'description' => 'Open top skip',
        ],
        'roll_off' => [
            'label' => 'Roll-off container',
            'color' => 'warning',
            'icon' => 'heroicon-o-truck',
            'description' => 'Hook-lift roll-off container',
        ],
        'pallet' => [
            'label' => 'Pallet',
            'color' => 'gray',
            'icon' => 'heroicon-o-rectangle-stack',
            'description' => 'Goods on a pallet',
        ],
        'bulk_pile' => [
            'label' => 'Bulk pile',
            'color' => 'gray',
            'icon' => 'heroicon-o-chart-pie',
            'description' => 'Loose material in a pile',
        ],
        'tank' => [
            'label' => 'Tank',
            'color' => 'danger',
            'icon' => 'heroicon-o-beaker',
            'description' => 'Fixed or mobile tank',
        ],
        'box' => [
            'label' => 'Box',
            'color' => 'gray',
            'icon' => 'heroicon-o-archive-box-arrow-down',
            'description' => 'Crate, box or rigid container',
        ],
        'other' => [
            'label' => 'Other',
            'color' => 'gray',
            'icon' => 'heroicon-o-question-mark-circle',
            'description' => 'Other container',
        ],
    ],
];
