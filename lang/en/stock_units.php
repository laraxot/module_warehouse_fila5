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
        'move' => [
            'label' => 'Put away / move',
            'icon' => 'heroicon-o-arrows-right-left',
            'tooltip' => 'Put away or move the unit to another location',
        ],
        'adjust_weight' => [
            'label' => 'Adjust weight',
            'icon' => 'heroicon-o-scale',
            'tooltip' => 'Record a net weight adjustment',
        ],
        'void' => [
            'label' => 'Void unit',
            'icon' => 'heroicon-o-no-symbol',
            'tooltip' => 'Void the unit (irreversible)',
        ],
    ],
];
