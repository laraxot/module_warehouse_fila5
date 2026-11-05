<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Unità di stoccaggio',
        'plural_label' => 'Unità di stoccaggio',
        'group' => 'Magazzino',
        'icon' => 'heroicon-o-cube',
        'sort' => 40,
    ],
    'label' => 'Unità di stoccaggio',
    'plural_label' => 'Unità di stoccaggio',
    'plural' => [
        'label' => 'Unità di stoccaggio',
    ],
    'model' => [
        'label' => 'Unità di stoccaggio',
        'plural' => 'Unità di stoccaggio',
    ],
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
        'print_label' => [
            'label' => 'Stampa etichetta',
            'icon' => 'heroicon-o-printer',
            'tooltip' => 'Stampa l\'etichetta con QR',
        ],
        'move' => [
            'label' => 'Stocca / sposta',
            'icon' => 'heroicon-o-arrows-right-left',
            'tooltip' => 'Stocca o sposta l\'unità in un\'altra posizione',
        ],
        'adjust_weight' => [
            'label' => 'Rettifica peso',
            'icon' => 'heroicon-o-scale',
            'tooltip' => 'Registra una rettifica del peso netto',
        ],
        'void' => [
            'label' => 'Annulla unità',
            'icon' => 'heroicon-o-no-symbol',
            'tooltip' => 'Annulla l\'unità (irreversibile)',
        ],
    ],
];
