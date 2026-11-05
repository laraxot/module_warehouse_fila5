<?php

declare(strict_types=1);

return [
    'label' => 'Tipo movimento',
    'values' => [
        'receipt' => [
            'label' => 'Carico',
            'color' => 'success',
            'icon' => 'heroicon-o-arrow-down-tray',
            'description' => 'Ingresso dall\'esterno',
        ],
        'putaway' => [
            'label' => 'Stoccaggio',
            'color' => 'info',
            'icon' => 'heroicon-o-map-pin',
            'description' => 'Prima messa in posizione',
        ],
        'transfer' => [
            'label' => 'Spostamento',
            'color' => 'info',
            'icon' => 'heroicon-o-arrows-right-left',
            'description' => 'Cambio di posizione',
        ],
        'consumption' => [
            'label' => 'Consumo',
            'color' => 'warning',
            'icon' => 'heroicon-o-fire',
            'description' => 'Prelievo per lavorazione',
        ],
        'production' => [
            'label' => 'Produzione',
            'color' => 'success',
            'icon' => 'heroicon-o-sparkles',
            'description' => 'Nata da una lavorazione',
        ],
        'shipment' => [
            'label' => 'Spedizione',
            'color' => 'primary',
            'icon' => 'heroicon-o-truck',
            'description' => 'Uscita verso terzi',
        ],
        'adjustment' => [
            'label' => 'Rettifica',
            'color' => 'gray',
            'icon' => 'heroicon-o-scale',
            'description' => 'Correzione del peso',
        ],
        'void' => [
            'label' => 'Annullamento',
            'color' => 'danger',
            'icon' => 'heroicon-o-no-symbol',
            'description' => 'Unità annullata',
        ],
    ],
];
