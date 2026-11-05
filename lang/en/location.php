<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Locations',
        'plural_label' => 'Locations',
        'group' => 'Warehouse',
        'icon' => 'heroicon-o-map-pin',
        'sort' => 30,
    ],
    'label' => 'Location',
    'plural_label' => 'Locations',
    'plural' => [
        'label' => 'Locations',
    ],
    'model' => [
        'label' => 'Location',
        'plural' => 'Locations',
    ],
    'fields' => [
        'area_id' => [
            'label' => 'Area',
        ],
        'area' => [
            'code' => [
                'label' => 'Area',
            ],
            'name' => [
                'label' => 'Area',
            ],
            'warehouse' => [
                'code' => [
                    'label' => 'Warehouse',
                ],
                'name' => [
                    'label' => 'Warehouse',
                ],
            ],
        ],
        'code' => [
            'label' => 'Code',
            'placeholder' => 'e.g. ST-01A',
            'helper_text' => 'Unique within the warehouse, convention {area}-{code}',
        ],
        'name' => [
            'label' => 'Description',
        ],
        'x' => [
            'label' => 'X (m)',
            'helper_text' => 'Distance from the left edge of the floor plan',
        ],
        'y' => [
            'label' => 'Y (m)',
            'helper_text' => 'Distance from the top edge of the floor plan',
        ],
        'max_weight_kg' => [
            'label' => 'Max load (kg)',
            'placeholder' => 'Empty = unlimited',
        ],
        'load_kg' => [
            'label' => 'Current load (kg)',
        ],
        'stock_units_count' => [
            'label' => 'Units here',
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
        'print_label' => [
            'label' => 'Print label',
            'icon' => 'heroicon-o-printer',
            'tooltip' => 'Print the QR label',
        ],
    ],
];
