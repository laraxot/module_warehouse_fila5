<?php

declare(strict_types=1);

return [
    'immutable_movement' => [
        'update' => 'Movement #:id cannot be changed: record an adjustment instead.',
        'delete' => 'Movement #:id cannot be deleted: movements are an immutable log.',
    ],
    'invalid_state' => [
        'transition' => 'Unit :code is ":from": it cannot become ":to".',
        'not_in_stock' => 'Unit :code is no longer in stock.',
    ],
    'invalid_quantity' => [
        'not_positive' => 'Quantity must be greater than zero (got :kg kg).',
        'negative' => 'Quantity cannot be negative (got :kg kg).',
        'exceeds_stock' => 'Unit :code holds :available kg: cannot take :kg.',
        'unchanged' => 'The weight given for :code equals the current one.',
    ],
    'location' => [
        'other_warehouse' => 'Location :location is not in the warehouse of unit :code.',
        'inactive' => 'Location :location is inactive.',
        'same_location' => 'Unit :code is already in location :location.',
        'code_already_used' => 'Location code :location is already used in this warehouse.',
    ],
    'area' => [
        'invalid_color' => 'Invalid colour ":color": use the hexadecimal format #rrggbb.',
    ],
    'geometry' => [
        'invalid' => 'Invalid polygon: use a JSON list of [x,y] vertices in metres.',
        'too_few_vertices' => 'The polygon needs at least :min vertices.',
        'negative' => 'Polygon coordinates cannot be negative.',
    ],
    'reason_required' => 'A reason is required.',
];
