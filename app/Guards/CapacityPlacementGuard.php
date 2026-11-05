<?php

declare(strict_types=1);

namespace Modules\Warehouse\Guards;

use Modules\Warehouse\Actions\GetAreaLoadKgAction;
use Modules\Warehouse\Actions\GetLocationLoadKgAction;
use Modules\Warehouse\Contracts\PlacementGuardContract;
use Modules\Warehouse\Datas\CapacityCheckData;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Portata massima: carico attuale + peso dell'unità entro `max_weight_kg` della posizione e dell'area.
 *
 * Se l'unità occupa già una posizione della stessa area/posizione il suo peso è già nel carico
 * attuale e non viene contato due volte (spostamento interno all'area).
 *
 * I carichi si leggono con lock condiviso (ultima versione confermata, non lo snapshot della
 * transazione): dentro PutAwayStockUnitAction area e posizione sono già bloccate, quindi il
 * controllo e la scrittura del movimento sono atomici rispetto agli altri posizionamenti.
 */
class CapacityPlacementGuard implements PlacementGuardContract
{
    public function __construct(
        private readonly GetLocationLoadKgAction $getLocationLoad,
        private readonly GetAreaLoadKgAction $getAreaLoad,
    ) {}

    public function deny(StockUnit $unit, Location $location): ?string
    {
        $unitKg = $unit->netWeightKg();
        $current = $unit->exists && $unit->isOccupying() ? $unit->location : null;

        $locationCheck = new CapacityCheckData(
            maxKg: $location->maxWeightKg(),
            loadKg: $this->getLocationLoad->execute($location),
            loadKg: $this->getLocationLoad->execute($location, lockingRead: true),
            incomingKg: $unitKg,
            alreadyIncludedKg: $current !== null && $current->id === $location->id ? $unitKg : 0.0,
        );

        if ($locationCheck->exceeds()) {
            return SafeStringCastAction::cast(__('warehouse::placement.capacity.location_exceeded', [
                'location' => $location->code,
                'max' => $locationCheck->maxKg,
                'load' => $locationCheck->loadKg,
                'incoming' => $unitKg,
            ]));
        }

        $area = $location->area;
        $areaCheck = new CapacityCheckData(
            maxKg: $area->maxWeightKg(),
            loadKg: $this->getAreaLoad->execute($area),
            loadKg: $this->getAreaLoad->execute($area, lockingRead: true),
            incomingKg: $unitKg,
            alreadyIncludedKg: $current !== null && $current->area_id === $area->id ? $unitKg : 0.0,
        );

        if ($areaCheck->exceeds()) {
            return SafeStringCastAction::cast(__('warehouse::placement.capacity.area_exceeded', [
                'area' => $area->code,
                'max' => $areaCheck->maxKg,
                'load' => $areaCheck->loadKg,
                'incoming' => $unitKg,
            ]));
        }

        return null;
    }
}
