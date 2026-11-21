<?php

declare(strict_types=1);

return [
    'immutable_movement' => [
        'update' => 'Il movimento #:id non può essere modificato: registra una rettifica.',
        'delete' => 'Il movimento #:id non può essere eliminato: i movimenti sono un registro immutabile.',
    ],
    'invalid_state' => [
        'transition' => 'L\'unità :code è ":from": non può passare a ":to".',
        'not_in_stock' => 'L\'unità :code non è più in giacenza.',
    ],
    'invalid_quantity' => [
        'not_positive' => 'La quantità deve essere maggiore di zero (ricevuto :kg kg).',
        'negative' => 'La quantità non può essere negativa (ricevuto :kg kg).',
        'exceeds_stock' => 'L\'unità :code ha :available kg: impossibile prelevarne :kg.',
        'unchanged' => 'Il peso indicato per :code è uguale a quello attuale.',
    ],
    'location' => [
        'other_warehouse' => 'La posizione :location non appartiene al magazzino dell\'unità :code.',
        'inactive' => 'La posizione :location è disattivata.',
        'same_location' => 'L\'unità :code è già nella posizione :location.',
        'code_already_used' => 'Il codice posizione :location è già usato in questo magazzino.',
    ],
    'area' => [
        'invalid_color' => 'Colore non valido ":color": usa il formato esadecimale #rrggbb.',
    ],
    'geometry' => [
        'invalid' => 'Poligono non valido: usa un elenco JSON di vertici [x,y] in metri.',
        'too_few_vertices' => 'Il poligono deve avere almeno :min vertici.',
        'negative' => 'Le coordinate del poligono non possono essere negative.',
    ],
    'reason_required' => 'Il motivo è obbligatorio.',
];
