---
type: story
tags: [warehouse, processing, waste, permessi, audit-sicurezza-a2, audit-sicurezza-a3, audit-sicurezza-m4]
slug: authorization-abilities
title: "Abilità dedicate per le azioni che cambiano dati e permessi seminati"
status: review
phase: REVIEW
owner: Warehouse
depends_on: []
related: []
created: 2026-11-06
updated: 2026-11-06
---

# Abilità dedicate per le azioni che cambiano dati e permessi seminati

## Story

Come **amministratore dell'impianto**
voglio **dare ai ruoli operativi solo i permessi che servono, senza che chi vede un record possa modificarlo**
così da **non dover dare il super-admin a operatori e capi impianto**.

## Perché / evidenze

- A2: abilità Filament non dichiarate nella policy (`deleteAny`, `reorder`, ...) erano consentite.
- A3: sposta/annulla/rettifica peso unità, bottoni dell'ordine, registra accettazione, import CSV EER controllavano
  solo lo stato del record; mappa e tracciabilità senza `canAccess`.
- M4: nessun seeder dei permessi: `hasPermissionTo()` lanciava `PermissionDoesNotExist` (500) per i non super-admin.

## Soluzione (Warehouse, Processing, Waste)

- `DeniesUndeclaredFilamentAbilities` in tutte le policy dei tre moduli.
- Abilità: `stock_unit.{move,adjustWeight,void}`; `processing_order.{start,complete,forceComplete,cancel,addInput,registerOutput}`;
  `fir.{change_status,record_acceptance}`, `ewc_code.import`, `register_entry.rectify`.
- `->authorize()` sui bottoni e `Gate::authorize()` nella callback; `canAccess()` su `WarehouseMapPage`
  (`warehouse.viewAny`) e `TraceabilityPage` (`processing_order.viewAny`), `canView()` su `AreaOccupancyWidget`.
- `WarehousePermissionSeeder`, `ProcessingPermissionSeeder`, `WastePermissionSeeder` (idempotenti, guard `web`),
  chiamati dai `*DatabaseSeeder`; test che ogni `hasPermissionTo()` delle policy sia nel seeder.

## Acceptance criteria

1. Con solo `view`/`update` i bottoni sposta/rettifica/annulla non sono autorizzati.
2. Ogni policy dei tre moduli nega `deleteAny`, `forceDeleteAny`, `restoreAny`, `reorder`.
3. Ogni permesso controllato dalle policy è creato dal seeder del modulo.

## Test

Warehouse `StockUnitActionAuthorizationTest`, `PermissionSeederTest`; Processing `ProcessingOrderActionAuthorizationTest`,
`PermissionSeederTest`; Waste `WastePolicyAbilitiesTest`, `PermissionSeederTest`.

## Handoff Roosko

`RooskoPermissionSeeder` assegna ai ruoli operativi i permessi; vanno aggiunti quelli nuovi:
`stock_unit.move` (operatore, accettazione), `stock_unit.adjustWeight` e `stock_unit.void` (responsabile tecnico),
`fir.change_status` e `fir.record_acceptance` (accettazione), `ewc_code.import` (responsabile tecnico).

## Dev agent record

- Agente: Claude (sessione audit Roosko). Verifica con la harness: Pest e PHPStan (app a zero errori).
