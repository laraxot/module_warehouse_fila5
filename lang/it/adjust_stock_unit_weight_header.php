<?php

declare(strict_types=1);

return [
    'label' => 'Rettifica peso',
    'modal_heading' => 'Rettifica peso di :code',
    'modal_description' => 'Peso netto attuale: :kg kg. La rettifica resta nei movimenti con il motivo indicato.',
    'success' => 'Peso di :code rettificato a :kg kg',
    'error' => 'Rettifica non consentita',
    'fields' => [
        'new_net_weight_kg' => [
            'label' => 'Nuovo peso netto (kg)',
        ],
        'reason' => [
            'label' => 'Motivo',
            'placeholder' => 'es. ripesata su bilancia a pianale',
        ],
    ],
];
