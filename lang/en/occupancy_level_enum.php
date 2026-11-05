<?php

declare(strict_types=1);

return [
    'label' => 'Occupancy level',
    'values' => [
        'empty' => [
            'label' => 'Empty',
            'color' => 'gray',
            'icon' => 'heroicon-o-minus-circle',
            'description' => 'No load',
        ],
        'low' => [
            'label' => 'Below 50%',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
            'description' => 'Plenty of room',
        ],
        'medium' => [
            'label' => '50–80%',
            'color' => 'info',
            'icon' => 'heroicon-o-information-circle',
            'description' => 'Medium occupancy',
        ],
        'high' => [
            'label' => '80–100%',
            'color' => 'warning',
            'icon' => 'heroicon-o-exclamation-triangle',
            'description' => 'Almost full',
        ],
        'full' => [
            'label' => 'Full',
            'color' => 'danger',
            'icon' => 'heroicon-o-x-circle',
            'description' => 'Max load reached or exceeded',
        ],
        'unbounded' => [
            'label' => 'Unbounded',
            'color' => 'primary',
            'icon' => 'heroicon-o-arrows-pointing-out',
            'description' => 'Area without a max load',
        ],
    ],
];
