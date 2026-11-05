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
                    'label' => 'Magazzino',
                ],
                'name' => [
                    'label' => 'Magazzino',
                ],
            ],
        ],
        'code' => [
            'label' => 'Codice',
            'placeholder' => 'es. ST-01A',
            'helper_text' => 'Univoco nel magazzino, convenzione {area}-{codice}',
        ],
        'name' => [
            'label' => 'Descrizione',
        ],
        'x' => [
            'label' => 'X (m)',
            'helper_text' => 'Distanza dal bordo sinistro della planimetria',
        ],
        'y' => [
            'label' => 'Y (m)',
            'helper_text' => 'Distanza dal bordo superiore della planimetria',
        ],
        'max_weight_kg' => [
            'label' => 'Portata massima (kg)',
            'placeholder' => 'Vuoto = illimitata',
        ],
        'load_kg' => [
            'label' => 'Carico attuale (kg)',
        ],
        'stock_units_count' => [
            'label' => 'Unità presenti',
        ],
        'is_active' => [
            'label' => 'Attiva',
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
