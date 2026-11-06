---
type: story
tags: [warehouse, sicurezza, upload, mappa, audit-sicurezza-m5, audit-sicurezza-b3]
slug: map-upload-and-area-color-safety
title: "Planimetria solo raster e colore delle aree validato"
status: review
phase: REVIEW
owner: Warehouse
depends_on: []
related: []
created: 2026-11-05
updated: 2026-11-05
---

# Planimetria solo raster e colore delle aree validato

## Story

Come **amministratore dell'impianto**
voglio che **la planimetria e i colori delle aree non possano trasportare codice**
così che **chi apre la mappa non esegua script o CSS inseriti da un altro utente**.

## Perché / evidenze

- Audit sicurezza M5: `Warehouse::registerMediaCollections()` accettava `image/svg+xml` sul disco `public`, servito
  dallo stesso dominio dei pannelli: un SVG con `<script>` aperto direttamente è un XSS memorizzato.
- Audit sicurezza B3: `warehouse-map.blade.php` scriveva `Area.color` in `style="stroke: ..."`; `ColorPicker` senza
  regola: `e()` non impedisce l'iniezione di proprietà CSS.

## Soluzione

- `Warehouse::MAP_MIME_TYPES` = PNG, JPEG, WebP; usato dalla media collection e dal campo upload.
- `Area::COLOR_PATTERN` (`#rrggbb`): `ColorPicker::hex()->regex()`, evento `saving` che lancia
  `InvalidAreaColorException` (stringa vuota → null), `Area::safeColor()`.
- Mappa: `MapAreaData::strokeColor()` scrive solo un esadecimale valido, altrimenti il colore di occupazione
  (difesa anche per dati salvati prima della regola).

## Acceptance criteria

1. Un SVG non è accettato come planimetria.
2. Un colore diverso da `#rrggbb` non si salva (form e modello).
3. La mappa non scrive mai nello `style` un colore non valido.

## Test

`tests/Feature/MapUploadAndColorTest.php`.

## Dev agent record

- Agente: Claude (sessione audit Roosko).
- File: `app/Models/Warehouse.php`, `app/Models/Area.php`, `app/Exceptions/InvalidAreaColorException.php`,
  `app/Filament/Resources/WarehouseResource/Schemas/WarehouseForm.php`, `app/Filament/Resources/AreaResource/Schemas/AreaForm.php`,
  `app/Actions/BuildWarehouseMapAction.php`, `app/Datas/MapAreaData.php`, `resources/views/filament/pages/warehouse-map.blade.php`,
  `lang/{it,en}/exceptions.php`.
- Rischio residuo: planimetrie SVG già caricate restano sul disco `public` finché non vengono sostituite.
- Disco privato degli atti autorizzativi: story Waste `authorization-document-private-disk`.
