<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Magazzini',
        'plural_label' => 'Magazzini',
        'group' => 'Magazzino',
        'icon' => 'heroicon-o-building-office-2',
        'sort' => 10,
    ],
    'label' => 'Magazzino',
    'plural_label' => 'Magazzini',
    'plural' => [
        'label' => 'Magazzini',
    ],
    'model' => [
        'label' => 'Magazzino',
        'plural' => 'Magazzini',
    ],
    'fields' => [
        'code' => [
            'label' => 'Codice',
            'placeholder' => 'es. MAG1',
            'helper_text' => 'Codice breve univoco, usato nei codici etichetta delle posizioni',
        ],
        'name' => [
            'label' => 'Nome',
            'placeholder' => 'Nome del magazzino',
        ],
        'description' => [
            'label' => 'Descrizione',
        ],
        'map_width_m' => [
            'label' => 'Larghezza planimetria (m)',
            'helper_text' => 'Se vuoto la mappa si adatta alle geometrie delle aree',
        ],
        'map_height_m' => [
            'label' => 'Profondità planimetria (m)',
            'helper_text' => 'Se vuoto la mappa si adatta alle geometrie delle aree',
        ],
        'map' => [
            'label' => 'Planimetria',
            'helper_text' => 'Immagine di sfondo della mappa (PNG, JPG, WebP o SVG), allineata a larghezza × profondità in metri',
        ],
        'is_active' => [
            'label' => 'Attivo',
        ],
        'areas_count' => [
            'label' => 'Aree',
        ],
        'stock_units_count' => [
            'label' => 'Unità',
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
