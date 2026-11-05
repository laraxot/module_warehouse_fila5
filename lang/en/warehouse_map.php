<?php

declare(strict_types=1);

return [
    'navigation' => [
        'label' => 'Warehouse map',
        'group' => 'Warehouse',
        'icon' => 'heroicon-o-map',
        'sort' => 5,
    ],
    'title' => 'Warehouse map',
    'fields' => [
        'warehouse_id' => [
            'label' => 'Warehouse',
        ],
    ],
    'svg' => [
        'aria_label' => 'Floor plan with areas and locations',
        'area_title' => ':code — :name: :load / :max kg (:pct%), :units units',
        'location_title' => ':code: :load kg',
    ],
    'units' => [
        'heading' => 'Units in :code — :name',
        'heading_empty' => 'Units in area',
        'total' => 'Total: :count units',
        'columns' => [
            'code' => 'Code',
            'location' => 'Location',
            'container_type' => 'Container',
            'status' => 'Status',
            'net_weight_kg' => 'Net weight (kg)',
        ],
    ],
    'messages' => [
        'no_warehouse' => 'No active warehouse: create one to see the map.',
        'no_geometry' => 'No area has a polygon yet: set the vertices in metres on each area.',
        'select_area' => 'Click an area on the map to list its units.',
        'no_units' => 'No units in this area.',
    ],
];
