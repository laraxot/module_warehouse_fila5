<?php

declare(strict_types=1);

return [
    'fields' => [
        'occurred_at' => [
            'label' => 'Data movimento',
        ],
        'stock_unit_id' => [
            'label' => 'Unità',
        ],
        'stockUnit' => [
            'code' => [
                'label' => 'Unità',
            ],
        ],
        'type' => [
            'label' => 'Tipo',
        ],
        'quantity_kg' => [
            'label' => 'Quantità (kg)',
        ],
        'weight_before_kg' => [
            'label' => 'Giacenza prima (kg)',
        ],
        'weight_after_kg' => [
            'label' => 'Giacenza dopo (kg)',
        ],
        'fromLocation' => [
            'code' => [
                'label' => 'Da posizione',
            ],
        ],
        'toLocation' => [
            'code' => [
                'label' => 'A posizione',
            ],
        ],
        'reference_type' => [
            'label' => 'Riferimento',
        ],
        'notes' => [
            'label' => 'Note / motivo',
        ],
        'occurred_from' => [
            'label' => 'Dal',
        ],
        'occurred_until' => [
            'label' => 'Al',
        ],
        'id' => [
            'label' => 'ID',
        ],
        'created_at' => [
            'label' => 'Creato il',
        ],
        'updated_at' => [
            'label' => 'Aggiornato il',
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'Nuovo',
            'icon' => 'heroicon-o-plus',
            'tooltip' => 'Crea un nuovo record',
        ],
        'edit' => [
            'label' => 'Modifica',
            'icon' => 'heroicon-o-pencil-square',
            'tooltip' => 'Modifica',
        ],
        'view' => [
            'label' => 'Dettaglio',
            'icon' => 'heroicon-o-eye',
            'tooltip' => 'Apri il dettaglio',
        ],
        'delete' => [
            'label' => 'Elimina',
            'icon' => 'heroicon-o-trash',
            'tooltip' => 'Elimina',
        ],
        'back' => [
            'label' => 'Indietro',
            'icon' => 'heroicon-o-arrow-left',
            'tooltip' => 'Torna all\'elenco',
        ],
        'layout' => [
            'label' => 'Vista',
            'icon' => 'heroicon-o-squares-2x2',
            'tooltip' => 'Cambia vista',
        ],
    ],
];
