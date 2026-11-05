<?php

declare(strict_types=1);

return [
    'label' => 'Adjust weight',
    'modal_heading' => 'Adjust weight of :code',
    'modal_description' => 'Current net weight: :kg kg. The adjustment is kept in the movements with the given reason.',
    'success' => 'Weight of :code adjusted to :kg kg',
    'error' => 'Adjustment not allowed',
    'fields' => [
        'new_net_weight_kg' => [
            'label' => 'New net weight (kg)',
        ],
        'reason' => [
            'label' => 'Reason',
            'placeholder' => 'e.g. re-weighed on platform scale',
        ],
    ],
];
