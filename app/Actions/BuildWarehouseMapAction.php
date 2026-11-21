<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Datas\MapAreaData;
use Modules\Warehouse\Datas\MapLocationData;
use Modules\Warehouse\Datas\OccupancyData;
use Modules\Warehouse\Datas\WarehouseMapData;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Spatie\QueueableAction\QueueableAction;

/**
 * Prepara i dati della mappa di un magazzino: aree con poligono e occupazione, posizioni con
 * coordinate e carico, dimensioni della planimetria (da `map_width_m`/`map_height_m`, altrimenti
 * dal riquadro che contiene tutte le geometrie) e URL della planimetria di sfondo.
 *
 * Numero di query fisso, indipendente da aree e posizioni: i carichi delle aree sono la somma in memoria
 * dei carichi delle loro posizioni (stessa regola di GetAreaLoadKgAction, posizioni eliminate comprese).
 */
class BuildWarehouseMapAction
{
    use QueueableAction;

    public const float DEFAULT_WIDTH_M = 100.0;

    public const float DEFAULT_HEIGHT_M = 60.0;

    public const float MARGIN_M = 2.0;

    public function __construct(
        private readonly ParseMapGeometryAction $parseGeometry,
    ) {}

    public function execute(Warehouse $warehouse): WarehouseMapData
    {
        $areas = Area::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        $locations = Location::query()
            ->whereIn('area_id', $areas->modelKeys())
            ->where('is_active', true)
            ->whereNotNull('x')
            ->whereNotNull('y')
            ->orderBy('code')
            ->get();

        /** @var array<int, float> $locationLoads */
        $locationLoads = [];
        /** @var array<int, int> $locationCounts */
        $locationCounts = [];
        $rows = StockUnit::query()
            ->occupying()
            ->whereIn('location_id', Location::withTrashed()->select('id')->whereIn('area_id', $areas->modelKeys()))
            ->get(['location_id', 'net_weight_kg']);
        foreach ($rows as $row) {
            if ($row->location_id === null) {
                continue;
            }

            $locationId = $row->location_id;
            $locationLoads[$locationId] = round(($locationLoads[$locationId] ?? 0.0) + $row->netWeightKg(), 3);
            $locationCounts[$locationId] = ($locationCounts[$locationId] ?? 0) + 1;
        }

        /** @var array<int, int> $areaUnitCounts */
        $areaUnitCounts = [];
        /** @var array<int, float> $areaLoads */
        $areaLoads = [];
        $occupiedLocations = Location::withTrashed()->whereKey(array_keys($locationCounts))->get(['id', 'area_id']);
        foreach ($occupiedLocations as $occupied) {
            $areaUnitCounts[$occupied->area_id] = ($areaUnitCounts[$occupied->area_id] ?? 0) + ($locationCounts[$occupied->id] ?? 0);
            $areaLoads[$occupied->area_id] = round(($areaLoads[$occupied->area_id] ?? 0.0) + ($locationLoads[$occupied->id] ?? 0.0), 3);
        }

        $mapAreas = [];
        foreach ($areas as $area) {
            $polygon = $this->polygonOf($area);
            if ($polygon === null) {
                continue;
            }

            $mapAreas[] = new MapAreaData(
                id: $area->id,
                code: $area->code,
                name: $area->name,
                type: $area->type,
                polygon: $polygon,
                color: $area->safeColor(),
                occupancy: new OccupancyData($areaLoads[$area->id] ?? 0.0, $area->maxWeightKg()),
                unitsCount: $areaUnitCounts[$area->id] ?? 0,
            );
        }

        $mapLocations = [];
        foreach ($locations as $location) {
            $mapLocations[] = new MapLocationData(
                id: $location->id,
                areaId: $location->area_id,
                code: $location->code,
                x: (float) $location->x,
                y: (float) $location->y,
                occupancy: new OccupancyData($locationLoads[$location->id] ?? 0.0, $location->maxWeightKg()),
            );
        }

        [$width, $height] = $this->dimensions($warehouse, $mapAreas, $mapLocations);

        return new WarehouseMapData(
            warehouseId: $warehouse->id,
            widthM: $width,
            heightM: $height,
            backgroundUrl: $warehouse->getMapUrl(),
            areas: $mapAreas,
            locations: $mapLocations,
        );
    }

    /**
     * @return list<array{0: float, 1: float}>|null
     */
    private function polygonOf(Area $area): ?array
    {
        try {
            return $this->parseGeometry->execute($area->map_geometry);
        } catch (\JsonException|\InvalidArgumentException $e) {
            report($e);

            return null;
        }
    }

    /**
     * @param  list<MapAreaData>  $areas
     * @param  list<MapLocationData>  $locations
     * @return array{0: float, 1: float}
     */
    private function dimensions(Warehouse $warehouse, array $areas, array $locations): array
    {
        if ($warehouse->map_width_m !== null && $warehouse->map_height_m !== null
            && (float) $warehouse->map_width_m > 0.0 && (float) $warehouse->map_height_m > 0.0) {
            return [(float) $warehouse->map_width_m, (float) $warehouse->map_height_m];
        }

        $maxX = 0.0;
        $maxY = 0.0;
        foreach ($areas as $area) {
            foreach ($area->polygon as [$x, $y]) {
                $maxX = max($maxX, $x);
                $maxY = max($maxY, $y);
            }
        }
        foreach ($locations as $location) {
            $maxX = max($maxX, $location->x);
            $maxY = max($maxY, $location->y);
        }

        if ($maxX <= 0.0 || $maxY <= 0.0) {
            return [self::DEFAULT_WIDTH_M, self::DEFAULT_HEIGHT_M];
        }

        return [round($maxX + self::MARGIN_M, 2), round($maxY + self::MARGIN_M, 2)];
    }
}
