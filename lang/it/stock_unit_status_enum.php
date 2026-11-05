<?php

declare(strict_types=1);

return [
    'label' => 'Stato unità',
    'values' => [
        'pending' => [
            'label' => 'Da stoccare',
            'color' => 'warning',
            'icon' => 'heroicon-o-clock',
            'description' => 'Registrata, non ancora in posizione',
        ],
        'stored' => [
            'label' => 'Stoccata',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
            'description' => 'In una posizione del magazzino',
        ],
        'reserved' => [
            'label' => 'Impegnata',
            'color' => 'info',
            'icon' => 'heroicon-o-lock-closed',
            'description' => 'In posizione ma riservata',
        ],
        'in_process' => [
            'label' => 'In lavorazione',
            'color' => 'primary',
            'icon' => 'heroicon-o-cog-6-tooth',
            'description' => 'Prelevata per una lavorazione',
        ],
        'consumed' => [
            'label' => 'Consumata',
            'color' => 'gray',
            'icon' => 'heroicon-o-fire',
            'description' => 'Interamente consumata',
        ],
        'shipped' => [
            'label' => 'Spedita',
            'color' => 'gray',
            'icon' => 'heroicon-o-truck',
            'description' => 'Uscita dal magazzino',
        ],
        'voided' => [
            'label' => 'Annullata',
            'color' => 'danger',
            'icon' => 'heroicon-o-no-symbol',
            'description' => 'Annullata',
        ],
    ],
];
