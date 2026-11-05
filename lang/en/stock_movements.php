<?php

declare(strict_types=1);

return [
    'fields' => [
        'occurred_at' => [
            'label' => 'Occurred at',
        ],
        'stock_unit_id' => [
            'label' => 'Unit',
        ],
        'stockUnit' => [
            'code' => [
                'label' => 'Unit',
            ],
        ],
        'type' => [
            'label' => 'Type',
        ],
        'quantity_kg' => [
            'label' => 'Quantity (kg)',
        ],
        'weight_before_kg' => [
            'label' => 'Stock before (kg)',
        ],
        'weight_after_kg' => [
            'label' => 'Stock after (kg)',
        ],
        'fromLocation' => [
            'code' => [
                'label' => 'From location',
            ],
        ],
        'toLocation' => [
            'code' => [
                'label' => 'To location',
            ],
        ],
        'reference_type' => [
            'label' => 'Reference',
        ],
        'notes' => [
            'label' => 'Notes / reason',
        ],
        'occurred_from' => [
            'label' => 'From',
        ],
        'occurred_until' => [
            'label' => 'Until',
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
