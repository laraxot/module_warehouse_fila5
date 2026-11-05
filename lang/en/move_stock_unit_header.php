<?php

declare(strict_types=1);

return [
    'label' => 'Put away / move',
    'modal_heading' => 'Put away or move :code',
    'modal_description' => 'Choose the target location: capacity and area rules are checked before moving.',
    'success' => ':code put away in :location',
    'error' => 'Move not allowed',
    'fields' => [
        'location_id' => [
            'label' => 'Target location',
            'placeholder' => 'Search by code',
        ],
    ],
];
