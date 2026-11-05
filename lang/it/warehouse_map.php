<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Mappa magazzino',
        'group' => 'Magazzino',
        'icon' => 'heroicon-o-map',
        'sort' => 5,
    ],
    'title' => 'Mappa del magazzino',
    'fields' => [
        'warehouse_id' => [
            'label' => 'Magazzino',
        ],
    ],
    'svg' => [
        'aria_label' => 'Planimetria con aree e posizioni',
        'area_title' => ':code — :name: :load / :max kg (:pct%), :units unità',
        'location_title' => ':code: :load kg',
    ],
    'units' => [
        'heading' => 'Unità in :code — :name',
        'heading_empty' => 'Unità presenti',
        'total' => 'Totale: :count unità',
        'columns' => [
            'code' => 'Codice',
            'location' => 'Posizione',
            'container_type' => 'Contenitore',
            'status' => 'Stato',
            'net_weight_kg' => 'Peso netto (kg)',
        ],
    ],
    'messages' => [
        'no_warehouse' => 'Nessun magazzino attivo: crea un magazzino per vedere la mappa.',
        'no_geometry' => 'Nessuna area ha un poligono: disegna le aree indicando i vertici in metri.',
        'select_area' => 'Fai click su un\'area della mappa per vedere le unità presenti.',
        'no_units' => 'Nessuna unità in quest\'area.',
    ],
];
