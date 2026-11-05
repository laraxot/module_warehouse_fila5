<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Areas',
        'plural_label' => 'Areas',
        'group' => 'Warehouse',
        'icon' => 'heroicon-o-square-3-stack-3d',
        'sort' => 20,
    ],
    'label' => 'Area',
    'plural_label' => 'Areas',
    'plural' => [
        'label' => 'Areas',
    ],
    'model' => [
        'label' => 'Area',
        'plural' => 'Areas',
    ],
    'fields' => [
        'warehouse_id' => [
            'label' => 'Warehouse',
        ],
        'warehouse' => [
            'code' => [
                'label' => 'Warehouse',
            ],
            'name' => [
                'label' => 'Warehouse',
            ],
        ],
        'code' => [
            'label' => 'Code',
            'placeholder' => 'e.g. ST',
            'helper_text' => 'Unique within the warehouse; prefix of location codes',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'type' => [
            'label' => 'Type',
        ],
        'max_weight_kg' => [
            'label' => 'Max load (kg)',
            'placeholder' => 'Empty = unlimited',
        ],
        'load_kg' => [
            'label' => 'Current load (kg)',
        ],
        'occupancy_pct' => [
            'label' => 'Occupancy (%)',
        ],
        'locations_count' => [
            'label' => 'Locations',
        ],
        'map_geometry' => [
            'label' => 'Floor plan polygon',
            'placeholder' => '[[0,0],[20,0],[20,15],[0,15]]',
            'helper_text' => 'Vertices [x,y] in metres, at least 3, JSON',
        ],
        'color' => [
            'label' => 'Border colour',
        ],
        'is_active' => [
            'label' => 'Active',
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
