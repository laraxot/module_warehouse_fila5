<?php

declare(strict_types=1);

return [
    'label' => 'Unit status',
    'values' => [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'icon' => 'heroicon-o-clock',
            'description' => 'Registered, not yet put away',
        ],
        'stored' => [
            'label' => 'Stored',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
            'description' => 'In a warehouse location',
        ],
        'reserved' => [
            'label' => 'Reserved',
            'color' => 'info',
            'icon' => 'heroicon-o-lock-closed',
            'description' => 'In place but reserved',
        ],
        'in_process' => [
            'label' => 'In process',
            'color' => 'primary',
            'icon' => 'heroicon-o-cog-6-tooth',
            'description' => 'Picked for processing',
        ],
        'consumed' => [
            'label' => 'Consumed',
            'color' => 'gray',
            'icon' => 'heroicon-o-fire',
            'description' => 'Fully consumed',
        ],
        'shipped' => [
            'label' => 'Shipped',
            'color' => 'gray',
            'icon' => 'heroicon-o-truck',
            'description' => 'Left the warehouse',
        ],
        'voided' => [
            'label' => 'Voided',
            'color' => 'danger',
            'icon' => 'heroicon-o-no-symbol',
            'description' => 'Voided',
        ],
    ],
];
