---
type: story
tags: [warehouse, concorrenza, capacita, audit-database-r4]
slug: placement-capacity-locks
title: "Portata di aree e posizioni rispettata anche con posizionamenti in parallelo"
status: review
phase: REVIEW
owner: Warehouse
depends_on: []
related: []
created: 2026-11-05
updated: 2026-11-05
---

# Portata di aree e posizioni rispettata anche con posizionamenti in parallelo

## Story

Come **responsabile dell'impianto**
voglio che **due operatori che stoccano nello stesso momento nella stessa area non possano superarne la portata**
così da **non superare mai i limiti fisici e autorizzativi di stoccaggio**, anche nelle ore di punta.

## Perché / evidenze

Audit database R4 (2026-11-05):

- `PutAwayStockUnitAction` bloccava solo l'unità che si sposta; il carico di posizione e area era letto
  con una `SUM` non bloccante (`GetLocationLoadKgAction`, `GetAreaLoadKgAction`) dentro
  `CapacityPlacementGuard`.
- Due unità diverse verso la stessa posizione/area passavano entrambe il controllo.
- In MySQL con REPEATABLE READ, se la transazione aveva già letto dalla connessione `warehouse`
  (es. generazione del codice in `CreateStockUnitAction`), la `SUM` leggeva lo snapshot iniziale.

## Soluzione

- `LockPlacementTargetAction`: `SELECT ... FOR UPDATE` sull'area e poi sulla posizione di destinazione;
  restituisce la posizione riletta con l'area bloccata come relazione.
- `PutAwayStockUnitAction`: nella transazione `warehouse` blocca area → posizione → unità, poi esegue i guard
  con la posizione bloccata.
- `SumNetWeightKgAction`: somma con lettura a lock condiviso (ultima versione confermata), sommata in PHP
  per non dipendere dal supporto di `FOR SHARE` con aggregazioni.
- `CapacityPlacementGuard` legge i carichi con `lockingRead: true`.
- `PlacementGuardContract` documenta il contesto (transazione, lock già presi, ordine dei lock) per i guard
  di altri moduli: il guard autorizzativo di Roosko blocca le proprie righe dentro `deny()`.

## Acceptance criteria

1. `PutAwayStockUnitAction` blocca area e posizione prima di bloccare l'unità e prima di qualunque guard.
2. I guard girano dentro la transazione `warehouse` e ricevono la posizione con l'area bloccata già caricata.
3. Una seconda unità che farebbe superare la portata della posizione o dell'area viene rifiutata.
4. Lettura bloccante e lettura normale dei carichi danno lo stesso valore.
5. Ordine dei lock documentato nel README e nel contratto dei guard.

## Test

`tests/Feature/PlacementLockingTest.php`: ordine lock → guard (spie), guard in transazione con area caricata,
portata di posizione e area, coerenza fra lettura bloccante e normale.

Non coperto: test con due processi reali su MySQL (il sqlite dei test condivide una sola connessione e ignora
i lock). Da eseguire su un ambiente MySQL di collaudo: due `putaway` paralleli da 60 kg su una posizione da 100 kg,
uno dei due deve fallire.

## Dev agent record

- Agente: Claude (sessione audit Roosko, compito Warehouse R4).
- File: `app/Actions/LockPlacementTargetAction.php`, `app/Actions/SumNetWeightKgAction.php`,
  `app/Actions/PutAwayStockUnitAction.php`, `app/Actions/GetLocationLoadKgAction.php`,
  `app/Actions/GetAreaLoadKgAction.php`, `app/Guards/CapacityPlacementGuard.php`,
  `app/Contracts/PlacementGuardContract.php`, `docs/README.md`, `tests/Feature/PlacementLockingTest.php`.
- Verifica: `php -l`, controllo dei riferimenti; test Pest non eseguiti (ambiente senza vendor).
- Handoff Roosko: `WasteAuthorizationPlacementGuard` può contare sul lock di area e posizione già preso;
  deve bloccare la riga `authorized_wastes` (connessione `waste`) dentro `deny()` e leggere le giacenze con
  letture bloccanti.
