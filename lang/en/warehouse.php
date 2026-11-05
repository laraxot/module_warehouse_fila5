<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Warehouses',
        'plural_label' => 'Warehouses',
        'group' => 'Warehouse',
        'icon' => 'heroicon-o-building-office-2',
        'sort' => 10,
    ],
    'label' => 'Warehouse',
    'plural_label' => 'Warehouses',
    'plural' => [
        'label' => 'Warehouses',
    ],
    'model' => [
        'label' => 'Warehouse',
        'plural' => 'Warehouses',
    ],
    'fields' => [
        'code' => [
            'label' => 'Code',
            'placeholder' => 'e.g. WH1',
            'helper_text' => 'Short unique code, used in location label codes',
        ],
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Warehouse name',
        ],
        'description' => [
            'label' => 'Description',
        ],
        'map_width_m' => [
            'label' => 'Map width (m)',
            'helper_text' => 'Leave empty to fit the map to the area geometries',
        ],
        'map_height_m' => [
            'label' => 'Map height (m)',
            'helper_text' => 'Leave empty to fit the map to the area geometries',
        ],
        'map' => [
            'label' => 'Floor plan',
            'helper_text' => 'Map background image (PNG, JPG, WebP or SVG), aligned to width × height in metres',
        ],
        'is_active' => [
            'label' => 'Active',
        ],
        'areas_count' => [
            'label' => 'Areas',
        ],
        'stock_units_count' => [
            'label' => 'Units',
        ],
        'id' => [
            'label' => 'ID',
        ],
        'created_at' => [
            'label' => 'Created at',
        ],
        'updated_at' => [
            'label' => 'Updated at',
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'New',
            'icon' => 'heroicon-o-plus',
            'tooltip' => 'Create a new record',
        ],
        'edit' => [
            'label' => 'Edit',
            'icon' => 'heroicon-o-pencil-square',
            'tooltip' => 'Edit',
        ],
        'view' => [
            'label' => 'View',
            'icon' => 'heroicon-o-eye',
            'tooltip' => 'Open details',
        ],
        'delete' => [
            'label' => 'Delete',
            'icon' => 'heroicon-o-trash',
            'tooltip' => 'Delete',
        ],
        'back' => [
            'label' => 'Back',
            'icon' => 'heroicon-o-arrow-left',
            'tooltip' => 'Back to the list',
        ],
        'layout' => [
            'label' => 'Layout',
            'icon' => 'heroicon-o-squares-2x2',
            'tooltip' => 'Switch layout',
        ],
    ],
];
