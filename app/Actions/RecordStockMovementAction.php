<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Exceptions\InvalidStockQuantityException;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * UNICO scrittore dei movimenti di magazzino.
 *
 * Registra il movimento con peso prima/dopo e applica all'unità gli effetti fisici del
 * movimento: peso netto (consumo, rettifica) e posizione (`$to` per putaway/transfer,
 * nessuna posizione per spedizione e annullamento). Lo stato dell'unità resta compito
 * dell'action di dominio che chiama (che sa perché il movimento avviene).
 *
 * `quantity_kg` è positivo per tutti i tipi tranne `adjustment`, dove è la differenza con segno.
 * Per i movimenti d'origine (receipt, production) il peso prima è 0.
 */
class RecordStockMovementAction
{
    use QueueableAction;

    public function execute(
        StockUnit $unit,
        MovementTypeEnum $type,
        float $quantityKg,
        ?Location $from,
        ?Location $to,
        ?Model $reference = null,
        ?string $notes = null,
    ): StockMovement {
        Assert::true($unit->exists, 'La unità di stoccaggio deve essere salvata prima di registrarne i movimenti.');

        $quantityKg = round($quantityKg, 3);
        if ($type !== MovementTypeEnum::ADJUSTMENT && $quantityKg < 0.0) {
            throw InvalidStockQuantityException::negative($quantityKg);
        }

        if ($type->isPlacement()) {
            Assert::notNull($to, 'Un movimento di '.$type->value.' richiede la posizione di destinazione.');
        }

        return $unit->getConnection()->transaction(function () use ($unit, $type, $quantityKg, $from, $to, $reference, $notes): StockMovement {
            $weightBefore = $type->isOrigin() ? 0.0 : $unit->netWeightKg();
            $weightAfter = $type->weightAfter($weightBefore, $quantityKg);

            if ($weightAfter < 0.0) {
                throw InvalidStockQuantityException::exceedsStock($unit, abs($quantityKg));
            }

            $movement = new StockMovement([
                'stock_unit_id' => $unit->id,
                'type' => $type,
                'quantity_kg' => $quantityKg,
                'weight_before_kg' => $weightBefore,
                'weight_after_kg' => $weightAfter,
                'from_location_id' => $from?->id,
                'to_location_id' => $to?->id,
                'occurred_at' => CarbonImmutable::now(),
                'notes' => $notes,
            ]);

            if ($reference !== null) {
                $movement->reference()->associate($reference);
            }

            $movement->save();

            if ($type->changesUnitWeight()) {
                $unit->net_weight_kg = number_format($weightAfter, 3, '.', '');
            }

            if ($type->isPlacement() && $to !== null) {
                $unit->location()->associate($to);
            }

            if ($type->isExit()) {
                $unit->location()->dissociate();
            }

            if ($unit->isDirty()) {
                $unit->save();
            }

            return $movement;
        });
    }
}
