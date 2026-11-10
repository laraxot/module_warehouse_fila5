# Modulo Warehouse

Magazzino mappato, generico e riusabile: magazzini con planimetria, aree con portata e poligono,
posizioni etichettabili, unità di stoccaggio tracciate, registro immutabile dei movimenti e
regole di posizionamento estendibili da altri moduli. Il modulo non sa nulla di rifiuti: il
contenuto di dominio di un'unità è il morph `subject` (in Roosko: `WasteLot`).

Dipendenze: `Xot`, `Labeling` (identificatori QR/RFID e stampa etichette). Connessione DB `warehouse`.

## Modelli

| Modello | Tabella | Note |
|---|---|---|
| `Warehouse` | `warehouses` | `code` univoco, `map_width_m`/`map_height_m`, media collection `map` (planimetria, file singolo, solo PNG/JPEG/WebP: niente SVG, che può contenere script) |
| `Area` | `areas` | `type` (`AreaTypeEnum`), `max_weight_kg` (null = illimitata), `map_geometry` `[[x,y],...]` in metri, `color` solo `#rrggbb` (validato nel form e nel modello: finisce nell'attributo `style` della mappa), unique `(warehouse_id, code)` |
| `Location` | `warehouse_locations` | `code` univoco nel magazzino (`{area}-{codice}`, garantito dal modello), `x`/`y` in metri, `max_weight_kg`; implementa `LabelableContract` (codice etichetta `LOC-{magazzino}-{codice}`) |
| `StockUnit` | `stock_units` | `code` `SU-{anno}-{6 cifre}`, `status` (`StockUnitStatusEnum`), `container_type`, `net_weight_kg`, `tare_weight_kg`, morph `subject`; implementa `LabelableContract` (codice etichetta = `code`) |
| `StockMovement` | `stock_movements` | registro solo-insert: update/delete lanciano `ImmutableStockMovementException`; morph `reference` |

Pesi in kg `decimal(14,3)`. Le FK verso altri moduli (`subject`, `reference`) sono indici senza vincolo.

Il carico di un'area/posizione è la somma di `net_weight_kg` delle unità `pending|stored|reserved`
presenti (`StockUnit::scopeOccupying`). Le unità `in_process` non occupano capacità.

## Actions

Tutte in `app/Actions`, Spatie Queueable Actions, in transazione sulla connessione del modello.
**`RecordStockMovementAction` è l'unico scrittore dei movimenti** e applica all'unità peso e posizione
del movimento; lo stato lo decide l'action di dominio che lo chiama.

```php
CreateStockUnitAction::execute(StockUnitData $data, MovementTypeEnum $origin = MovementTypeEnum::RECEIPT, ?Model $reference = null): StockUnit
CheckPlacementAction::execute(StockUnit $unit, Location $location): ?string
PutAwayStockUnitAction::execute(StockUnit $unit, Location $location, ?Model $reference = null): StockUnit
ConsumeStockUnitAction::execute(StockUnit $unit, float $kg, ?Model $reference = null): StockMovement
ShipStockUnitAction::execute(StockUnit $unit, ?Model $reference = null): StockMovement
AdjustStockUnitWeightAction::execute(StockUnit $unit, float $newNetKg, string $reason): StockMovement
VoidStockUnitAction::execute(StockUnit $unit, string $reason): StockMovement
RecordStockMovementAction::execute(StockUnit $unit, MovementTypeEnum $type, float $quantityKg, ?Location $from, ?Location $to, ?Model $reference = null, ?string $notes = null): StockMovement
GetAreaLoadKgAction::execute(Area $area): float
GetLocationLoadKgAction::execute(Location $location): float
```

Di supporto: `GenerateStockUnitCodeAction` (codice con `lockForUpdate`), `LockStockUnitAction`,
GetAreaLoadKgAction::execute(Area $area, bool $lockingRead = false): float
GetLocationLoadKgAction::execute(Location $location, bool $lockingRead = false): float
LockPlacementTargetAction::execute(Location $location): Location
```

Di supporto: `GenerateStockUnitCodeAction` (codice con `lockForUpdate`), `LockStockUnitAction`, `SumNetWeightKgAction`,
`GetAreaOccupancyAction`, `BuildWarehouseMapAction`, `ListAreaStockUnitsAction`, `ParseMapGeometryAction`.

Le action modificano anche l'istanza passata (peso, posizione, stato): chi chiama vede subito lo stato aggiornato.

### Codice unità

`SU-{anno}-{progressivo a 6 cifre}`. `stock_units.code` è univoco su tutta l'installazione ed è il
valore codificato nel QR (Labeling richiede identificatori univoci): il progressivo è quindi **per
anno su tutti i magazzini**. Il generatore blocca la riga del magazzino e l'ultimo codice dell'anno
(`lockForUpdate`) dentro la transazione di creazione e non riusa i codici delle unità eliminate.

### Movimenti

| Tipo | Quantità | Giacenza dopo | Effetto sull'unità |
|---|---|---|---|
| `receipt`, `production` | peso iniziale | = quantità | (origine) |
| `putaway`, `transfer` | peso attuale | invariata | posizione = `to` |
| `consumption` | kg consumati | prima − quantità | peso netto ridotto; a 0 → `consumed`, nessuna posizione |
| `adjustment` | differenza con segno | prima + differenza | peso netto = nuovo peso |
| `shipment`, `void` | peso attuale | 0 | nessuna posizione (il peso netto resta quello spedito/annullato) |

### Concorrenza: capacità e lock

Il controllo di portata è un *check-then-act*: senza lock due posizionamenti paralleli verso la stessa
posizione o area leggono lo stesso carico e passano entrambi (audit database R4).
`PutAwayStockUnitAction` lavora in una transazione sulla connessione `warehouse` e blocca, in quest'ordine:

1. l'**area** di destinazione (`SELECT ... FOR UPDATE`, `LockPlacementTargetAction`);
2. la **posizione** di destinazione (idem; la posizione restituita ha l'area bloccata come relazione);
3. l'**unità** (`LockStockUnitAction`);

e solo dopo esegue i guard. I carichi letti dai guard usano una lettura con lock condiviso
(`GetLocationLoadKgAction`/`GetAreaLoadKgAction` con `lockingRead: true`, `SumNetWeightKgAction`): in
MySQL con REPEATABLE READ una `SELECT SUM` normale leggerebbe lo snapshot iniziale della transazione e
non vedrebbe l'unità appena stoccata da un'altra sessione.

L'ordine area → posizione → unità è lo stesso ovunque: le action che bloccano solo l'unità (rettifica,
annullo, consumo, uscita) prendono solo l'ultimo anello, quindi non si creano cicli. Un guard di un altro
modulo che blocca righe proprie lo fa dentro `deny()`, cioè **dopo** i lock di Warehouse.
`CheckPlacementAction` chiamata da sola (fuori da `PutAwayStockUnitAction`) non blocca nulla: serve a
mostrare in anticipo il motivo di un rifiuto, ma l'esito vincolante è quello dentro la transazione.

### Carichi nelle liste (niente query per riga)

`Area::withLoadKg()` e `Location::withLoadKg()` aggiungono la colonna `load_kg` alla query; le action di carico
la usano se presente. Tabelle e relation manager di aree e posizioni, `AreaOccupancyWidget` e la mappa
eseguono un numero fisso di query (audit database R13). Indice `stock_units (warehouse_id, status, location_id)`
(migrazione `2026_11_06_100500_add_query_supporting_indexes`, `ensureIndexes()`).

### Stati

`StockUnitStatusEnum::canTransitionTo()` definisce le transizioni; le action fanno
`Assert::true($unit->status->canTransitionTo($to), ...)`. `consumed`, `shipped`, `voided` sono finali.

## Punti di estensione

### Guard di posizionamento

Ogni posizionamento (creazione con posizione, putaway, transfer) passa da `CheckPlacementAction`,
che consulta in ordine i guard di `config('warehouse.placement_guards')`. Il primo motivo non nullo
blocca l'operazione con `PlacementDeniedException` (messaggio = motivo).

Il modulo registra `CapacityPlacementGuard` (portata di posizione e area; un'unità che si sposta
nella stessa area non viene contata due volte).

Per aggiungere un guard da un altro modulo:

```php
namespace Modules\Roosko\Guards;

use Modules\Warehouse\Contracts\PlacementGuardContract;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;

class WasteAuthorizationPlacementGuard implements PlacementGuardContract
{
    public function deny(StockUnit $unit, Location $location): ?string
    {
        // null = consentito; altrimenti il motivo GIÀ TRADOTTO
        return null;
    }
}
```

```php
// Modules/Roosko/app/Providers/RooskoServiceProvider.php
public function boot(): void
{
    parent::boot();

    config()->push('warehouse.placement_guards', WasteAuthorizationPlacementGuard::class);
}
```

Dentro `PutAwayStockUnitAction` il guard gira nella transazione `warehouse`, dopo i lock di area,
posizione e unità (vedi *Concorrenza*): `$location` è la riga bloccata con `area` già caricata. Un
guard che somma giacenze deve usare letture bloccanti; se blocca righe di un altro modulo (es. il
titolo autorizzativo in Waste) lo fa dentro `deny()` e sempre nello stesso ordine.

Il ServiceProvider di Warehouse unisce la propria config in `register()`, quindi il `push` nel `boot()`
di un altro modulo trova sempre il default del modulo. I guard sono risolti dal container (dipendenze
nel costruttore) e vengono chiamati anche per unità non ancora salvate: non contare su `$unit->exists`.
Una voce che non implementa il contratto lancia `InvalidPlacementGuardException`.

## Filament

- `WarehouseResource` (RM aree, upload planimetria), `AreaResource` (RM posizioni), `LocationResource`
  (stampa etichetta in tabella e in modifica), `StockUnitResource` (creazione tramite `CreateStockUnitAction`;
  dettaglio con infolist, RM movimenti in sola lettura e header actions *stampa etichetta*, *stocca/sposta*,
  *rettifica peso*, *annulla*), `StockMovementResource` (solo elenco con filtri e dettaglio in modale).
- Pagina `WarehouseMapPage`: SVG generato lato server con le aree colorate per occupazione
  (`OccupancyLevelEnum`, colori Filament), i punti posizione, la legenda e la planimetria di sfondo;
  click su un'area → elenco delle unità presenti.
- Widget `AreaOccupancyWidget`: occupazione per area (sulla mappa, filtrato sul magazzino scelto).

Le Filament actions (`app/Filament/Actions`) chiamano solo le Actions di dominio. Etichette e icone
dei bottoni stanno in `lang/{it,en}/stock_unit.php` (`actions.*`), i testi dei modali nel file lang
della action (`move_stock_unit_header.php`, ...). Enum, pagina e widget hanno il proprio file lang.

## Dati demo

`WarehouseDatabaseSeeder`: magazzino `DEMO` 80 × 50 m con le aree `IN` (ricevimento), `QU` (quarantena),
`ST` (stoccaggio), `PR` (lavorazione), `OUT` (spedizione), griglia di posizioni `{area}-{colonna}{riga}`
e alcune unità create con `CreateStockUnitAction`.

## Test

`tests/Unit`: guard di capacità (carichi simulati), generatore di codice, enum.
`tests/Feature`: creazione, putaway/transfer, consumo, spedizione, rettifica, annullamento con
verifica dei movimenti, immutabilità del registro, guard registrato da config.
verifica dei movimenti, immutabilità del registro, guard registrato da config, ordine dei lock
e portata (`PlacementLockingTest`).

Storie: [`docs/stories/`](stories/).

## Migrazioni

Le migrazioni di `stock_units`, `stock_movements` hanno un `down()` che rifiuta il rollback: sono dati legali o di tracciabilità e non si
cancellano con `migrate:rollback` (audit database R15). Unique e soft delete: decisioni nella story
[`legal-tables-migrations`](../../Waste/docs/stories/legal-tables-migrations.story.md) del modulo Waste.

## Permessi

Policy in `app/Models/Policies`, tutte con `DeniesUndeclaredFilamentAbilities` (eliminazione massiva, ripristino,
riordino, collegamenti negati se non dichiarati). Permessi creati da `WarehousePermissionSeeder` (idempotente,
guard `web`, chiamato da `WarehouseDatabaseSeeder`).

| Bottone / pagina | Abilità | Permesso |
|---|---|---|
| Stocca/sposta unità | `move` | `stock_unit.move` |
| Rettifica peso | `adjustWeight` | `stock_unit.adjustWeight` |
| Annulla unità | `void` | `stock_unit.void` |
| Mappa del magazzino (`canAccess`) | `viewAny` Warehouse | `warehouse.viewAny` |
| Widget occupazione (`canView`) | `viewAny` Area | `area.viewAny` |

I bottoni usano `->authorize()` e ricontrollano con `Gate::authorize()` nella callback.
