<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => [
            'label' => 'Code',
        ],
        'status' => [
            'label' => 'Status',
        ],
        'container_type' => [
            'label' => 'Container',
        ],
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
        'location_id' => [
            'label' => 'Location',
            'placeholder' => 'Put away later',
            'helper_text' => 'If set, the unit is put away immediately (capacity and rules are checked)',
        ],
        'location' => [
            'code' => [
                'label' => 'Location',
            ],
            'area' => [
                'name' => [
                    'label' => 'Area',
                ],
            ],
        ],
        'net_weight_kg' => [
            'label' => 'Net weight (kg)',
        ],
        'tare_weight_kg' => [
            'label' => 'Tare (kg)',
        ],
        'gross_weight_kg' => [
            'label' => 'Gross weight (kg)',
        ],
        'subject_type' => [
            'label' => 'Content',
        ],
        'subject_id' => [
            'label' => 'Content ID',
        ],
        'received_at' => [
            'label' => 'Registered at',
        ],
        'notes' => [
            'label' => 'Notes',
        ],
        'in_stock' => [
            'label' => 'In stock only',
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
