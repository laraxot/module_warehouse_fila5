<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Exceptions\InvalidStockQuantityException;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Crea una unità di stoccaggio: codice `SU-{anno}-{progressivo}`, movimento d'origine
 * (`receipt` o `production`) e, se `locationId` è valorizzato, posizionamento con verifica
 * dei guard (PutAwayStockUnitAction). Tutto in una transazione: un rifiuto del guard annulla
 * anche la creazione.
 */
class CreateStockUnitAction
{
    use QueueableAction;

    public function __construct(
        private readonly GenerateStockUnitCodeAction $generateCode,
        private readonly RecordStockMovementAction $recordMovement,
        private readonly PutAwayStockUnitAction $putAway,
    ) {}

    public function execute(StockUnitData $data, MovementTypeEnum $origin = MovementTypeEnum::RECEIPT, ?Model $reference = null): StockUnit
    {
        Assert::true($origin->isOrigin(), 'Il movimento d\'origine deve essere receipt o production, ricevuto: '.$origin->value);

        if ($data->netWeightKg <= 0.0) {
            throw InvalidStockQuantityException::notPositive($data->netWeightKg);
        }

        if ($data->tareWeightKg !== null && $data->tareWeightKg < 0.0) {
            throw InvalidStockQuantityException::negative($data->tareWeightKg);
        }

        $warehouse = Warehouse::query()->findOrFail($data->warehouseId);
        $location = $data->locationId === null ? null : Location::query()->findOrFail($data->locationId);

        return $warehouse->getConnection()->transaction(function () use ($data, $origin, $reference, $warehouse, $location): StockUnit {
            $now = CarbonImmutable::now();

            $unit = new StockUnit([
                'code' => $this->generateCode->execute($warehouse, $now),
                'container_type' => $data->containerType,
                'status' => StockUnitStatusEnum::PENDING,
                'net_weight_kg' => round($data->netWeightKg, 3),
                'tare_weight_kg' => $data->tareWeightKg === null ? null : round($data->tareWeightKg, 3),
                'received_at' => $now,
                'notes' => $data->notes,
            ]);
            $unit->warehouse()->associate($warehouse);

            $subject = $data->subjectModel();
            if ($subject !== null) {
                $unit->subject()->associate($subject);
            }

            $unit->save();

            $this->recordMovement->execute($unit, $origin, $unit->netWeightKg(), null, null, $reference);

            if ($location !== null) {
                $this->putAway->execute($unit, $location, $reference);
            }

            return $unit->refresh();
        });
    }
}
