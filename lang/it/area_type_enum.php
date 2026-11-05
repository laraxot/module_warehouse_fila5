<?php

declare(strict_types=1);

return [
    'label' => 'Tipo area',
    'values' => [
        'inbound' => [
            'label' => 'Ricevimento',
            'color' => 'info',
            'icon' => 'heroicon-o-arrow-down-tray',
            'description' => 'Area di scarico e accettazione',
        ],
        'quarantine' => [
            'label' => 'Quarantena',
            'color' => 'warning',
            'icon' => 'heroicon-o-shield-exclamation',
            'description' => 'In attesa di verifiche o analisi',
        ],
        'storage' => [
            'label' => 'Stoccaggio',
            'color' => 'success',
            'icon' => 'heroicon-o-archive-box',
            'description' => 'Messa in riserva / deposito',
        ],
        'processing' => [
            'label' => 'Lavorazione',
            'color' => 'primary',
            'icon' => 'heroicon-o-cog-6-tooth',
            'description' => 'A bordo macchina per il trattamento',
        ],
        'outbound' => [
            'label' => 'Spedizione',
            'color' => 'danger',
            'icon' => 'heroicon-o-arrow-up-tray',
            'description' => 'Pronto per l\'uscita',
        ],
    ],
];
