<?php

declare(strict_types=1);

return [
    'label' => 'Annulla unità',
    'modal_heading' => 'Annullare :code?',
    'modal_description' => 'L\'unità esce dalla giacenza e libera la posizione. La storia dei movimenti resta consultabile. L\'operazione non è reversibile.',
    'success' => ':code annullata',
    'error' => 'Annullamento non consentito',
    'fields' => [
        'reason' => [
            'label' => 'Motivo',
            'placeholder' => 'es. registrata per errore',
        ],
    ],
];
