<?php

declare(strict_types=1);

return [
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
];
