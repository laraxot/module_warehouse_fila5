---
type: story
tags: [warehouse, performance, indici, audit-database-r13, audit-database-r14]
slug: load-queries-and-indexes
title: "Carichi di aree e posizioni senza una query per riga, indice per i riepiloghi"
status: review
phase: REVIEW
owner: Warehouse
depends_on: []
related: []
created: 2026-11-06
updated: 2026-11-06
---

# Carichi di aree e posizioni senza una query per riga, indice per i riepiloghi

## Story

Come **operatore che tiene aperta la dashboard e la mappa del magazzino**
voglio che **tabelle, widget e mappa restino veloci anche con molte aree e posizioni**
così che **il polling di 60 secondi non carichi il database con una SUM per riga**.

## Perché / evidenze

Audit database R13: `LocationsTable` (una SUM per posizione), `AreasTable` (tre chiamate per area alla stessa
action), `AreaOccupancyWidget` (una SUM per area a ogni polling), `BuildWarehouseMapAction` (una SUM per area
mentre i carichi per posizione erano già in memoria). Audit R14: nessun indice per i riepiloghi
`warehouse_id + status + location_id` di `stock_units`.

## Soluzione

- Scope `Area::withLoadKg()` (sottoquery correlata, posizioni eliminate comprese) e `Location::withLoadKg()`
  (`withSum` sulle unità che occupano); `GetAreaLoadKgAction`/`GetLocationLoadKgAction` usano il valore già
  caricato (`preloadedLoadKg()`), tranne la lettura bloccante dei guard.
- Tabelle e relation manager di aree e posizioni: `table()->modifyQueryUsing(withLoadKg)`; widget con `withLoadKg()`.
- Mappa: carico d'area sommato in memoria dai carichi delle posizioni.
- Migrazione `2026_11_06_100500_add_query_supporting_indexes` (`ensureIndexes`, idempotente):
  `stock_units (warehouse_id, status, location_id)`.

## Acceptance criteria

1. Elenco di N aree o posizioni con carico: una query, qualunque sia N.
2. Mappa: numero di query indipendente dal numero di aree.
3. Indice presente, migrazione rieseguibile.

## Test

`tests/Feature/LoadQueriesTest.php`, `tests/Feature/QuerySupportingIndexesTest.php`.

## Dev agent record

- Agente: Claude (sessione audit Roosko). Verifica con la harness (Pest + PHPStan).
