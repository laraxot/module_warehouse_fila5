<?php

declare(strict_types=1);

return [
    'label' => 'Area type',
    'values' => [
        'inbound' => [
            'label' => 'Inbound',
            'color' => 'info',
            'icon' => 'heroicon-o-arrow-down-tray',
            'description' => 'Unloading and acceptance area',
        ],
        'quarantine' => [
            'label' => 'Quarantine',
            'color' => 'warning',
            'icon' => 'heroicon-o-shield-exclamation',
            'description' => 'Awaiting checks or analyses',
        ],
        'storage' => [
            'label' => 'Storage',
            'color' => 'success',
            'icon' => 'heroicon-o-archive-box',
            'description' => 'Storage',
        ],
        'processing' => [
            'label' => 'Processing',
            'color' => 'primary',
            'icon' => 'heroicon-o-cog-6-tooth',
            'description' => 'Machine side, for treatment',
        ],
        'outbound' => [
            'label' => 'Outbound',
            'color' => 'danger',
            'icon' => 'heroicon-o-arrow-up-tray',
            'description' => 'Ready for dispatch',
        ],
    ],
];
