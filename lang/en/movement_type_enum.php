<?php

declare(strict_types=1);

return [
    'label' => 'Movement type',
    'values' => [
        'receipt' => [
            'label' => 'Receipt',
            'color' => 'success',
            'icon' => 'heroicon-o-arrow-down-tray',
            'description' => 'Incoming from outside',
        ],
        'putaway' => [
            'label' => 'Put away',
            'color' => 'info',
            'icon' => 'heroicon-o-map-pin',
            'description' => 'First placement',
        ],
        'transfer' => [
            'label' => 'Transfer',
            'color' => 'info',
            'icon' => 'heroicon-o-arrows-right-left',
            'description' => 'Location change',
        ],
        'consumption' => [
            'label' => 'Consumption',
            'color' => 'warning',
            'icon' => 'heroicon-o-fire',
            'description' => 'Taken for processing',
        ],
        'production' => [
            'label' => 'Production',
            'color' => 'success',
            'icon' => 'heroicon-o-sparkles',
            'description' => 'Output of processing',
        ],
        'shipment' => [
            'label' => 'Shipment',
            'color' => 'primary',
            'icon' => 'heroicon-o-truck',
            'description' => 'Dispatched to third parties',
        ],
        'adjustment' => [
            'label' => 'Adjustment',
            'color' => 'gray',
            'icon' => 'heroicon-o-scale',
            'description' => 'Weight correction',
        ],
        'void' => [
            'label' => 'Void',
            'color' => 'danger',
            'icon' => 'heroicon-o-no-symbol',
            'description' => 'Unit voided',
        ],
    ],
];
