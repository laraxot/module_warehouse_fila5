<?php

declare(strict_types=1);

return [
    'label' => 'Livello di occupazione',
    'values' => [
        'empty' => [
            'label' => 'Vuota',
            'color' => 'gray',
            'icon' => 'heroicon-o-minus-circle',
            'description' => 'Nessun carico',
        ],
        'low' => [
            'label' => 'Sotto il 50%',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
            'description' => 'Ampia disponibilità',
        ],
        'medium' => [
            'label' => '50–80%',
            'color' => 'info',
            'icon' => 'heroicon-o-information-circle',
            'description' => 'Occupazione media',
        ],
        'high' => [
            'label' => '80–100%',
            'color' => 'warning',
            'icon' => 'heroicon-o-exclamation-triangle',
            'description' => 'Quasi piena',
        ],
        'full' => [
            'label' => 'Piena',
            'color' => 'danger',
            'icon' => 'heroicon-o-x-circle',
            'description' => 'Portata raggiunta o superata',
        ],
        'unbounded' => [
            'label' => 'Senza limite',
            'color' => 'primary',
            'icon' => 'heroicon-o-arrows-pointing-out',
            'description' => 'Area senza portata massima',
        ],
    ],
];
