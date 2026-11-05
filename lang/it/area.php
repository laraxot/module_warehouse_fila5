<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Aree',
        'plural_label' => 'Aree',
        'group' => 'Magazzino',
        'icon' => 'heroicon-o-square-3-stack-3d',
        'sort' => 20,
    ],
    'label' => 'Area',
    'plural_label' => 'Aree',
    'plural' => [
        'label' => 'Aree',
    ],
    'model' => [
        'label' => 'Area',
        'plural' => 'Aree',
    ],
    'fields' => [
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
        'code' => [
            'label' => 'Codice',
            'placeholder' => 'es. ST',
            'helper_text' => 'Univoco nel magazzino; prefisso dei codici posizione',
        ],
        'name' => [
            'label' => 'Nome',
        ],
        'type' => [
            'label' => 'Tipo',
        ],
        'max_weight_kg' => [
            'label' => 'Portata massima (kg)',
            'placeholder' => 'Vuoto = illimitata',
        ],
        'load_kg' => [
            'label' => 'Carico attuale (kg)',
        ],
        'occupancy_pct' => [
            'label' => 'Occupazione (%)',
        ],
        'locations_count' => [
            'label' => 'Posizioni',
        ],
        'map_geometry' => [
            'label' => 'Poligono sulla planimetria',
            'placeholder' => '[[0,0],[20,0],[20,15],[0,15]]',
            'helper_text' => 'Vertici [x,y] in metri, almeno 3, JSON',
        ],
        'color' => [
            'label' => 'Colore bordo',
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
