<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => [
            'label' => 'Codice',
        ],
        'status' => [
            'label' => 'Stato',
        ],
        'container_type' => [
            'label' => 'Contenitore',
        ],
        'warehouse_id' => [
            'label' => 'Magazzino',
        ],
        'warehouse' => [
            'code' => [
                'label' => 'Magazzino',
            ],
            'name' => [
                'label' => 'Magazzino',
            ],
        ],
        'location_id' => [
            'label' => 'Posizione',
            'placeholder' => 'Da stoccare in seguito',
            'helper_text' => 'Se indicata, l\'unità viene subito stoccata (con verifica di portata e regole)',
        ],
        'location' => [
            'code' => [
                'label' => 'Posizione',
            ],
            'area' => [
                'name' => [
                    'label' => 'Area',
                ],
            ],
        ],
        'net_weight_kg' => [
            'label' => 'Peso netto (kg)',
        ],
        'tare_weight_kg' => [
            'label' => 'Tara (kg)',
        ],
        'gross_weight_kg' => [
            'label' => 'Peso lordo (kg)',
        ],
        'subject_type' => [
            'label' => 'Contenuto',
        ],
        'subject_id' => [
            'label' => 'ID contenuto',
        ],
        'received_at' => [
            'label' => 'Registrata il',
        ],
        'notes' => [
            'label' => 'Note',
        ],
        'in_stock' => [
            'label' => 'Solo in giacenza',
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
];
