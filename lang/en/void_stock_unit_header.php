<?php

declare(strict_types=1);

return [
    'label' => 'Void unit',
    'modal_heading' => 'Void :code?',
    'modal_description' => 'The unit leaves the stock and frees its location. Its movement history is kept. This cannot be undone.',
    'success' => ':code voided',
    'error' => 'Void not allowed',
    'fields' => [
        'reason' => [
            'label' => 'Reason',
            'placeholder' => 'e.g. registered by mistake',
        ],
    ],
];
