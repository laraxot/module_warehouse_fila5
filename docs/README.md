# Modulo Warehouse

Magazzino mappato, generico e riusabile: magazzini con planimetria, aree con portata e poligono,
posizioni etichettabili, unità di stoccaggio tracciate, registro immutabile dei movimenti e
regole di posizionamento estendibili da altri moduli. Il modulo non sa nulla di rifiuti: il
contenuto di dominio di un'unità è il morph `subject` (in Roosko: `WasteLot`).

Dipendenze: `Xot`, `Labeling` (identificatori QR/RFID e stampa etichette). Connessione DB `warehouse`.

## Modelli

| Modello | Tabella | Note |
|---|---|---|
| `Warehouse` | `warehouses` | `code` univoco, `map_width_m`/`map_height_m`, media collection `map` (planimetria, file singolo) |
| `Area` | `areas` | `type` (`AreaTypeEnum`), `max_weight_kg` (null = illimitata), `map_geometry` `[[x,y],...]` in metri, unique `(warehouse_id, code)` |
| `Location` | `locations` | `code` univoco nel magazzino (`{area}-{codice}`, garantito dal modello), `x`/`y` in metri, `max_weight_kg`; implementa `LabelableContract` (codice etichetta `LOC-{magazzino}-{codice}`) |
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
