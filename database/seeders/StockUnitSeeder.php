<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\Warehouse;

/**
 * Alcune unità demo nel magazzino `DEMO`, create con CreateStockUnitAction (codice, movimenti e guard reali).
 * Non fa nulla se il magazzino ha già unità.
 */
class StockUnitSeeder extends Seeder
{
    /**
     * location code => [contenitore, peso netto kg, tara kg]
     *
     * @var array<string, array{0: ContainerTypeEnum, 1: float, 2: float|null}>
     */
    private const array UNITS = [
        'IN-01A' => [ContainerTypeEnum::BIG_BAG, 850.0, 4.5],
        'IN-02A' => [ContainerTypeEnum::ROLL_OFF, 6200.0, null],
        'QU-01A' => [ContainerTypeEnum::DRUM, 180.0, 18.0],
        'ST-01A' => [ContainerTypeEnum::BIG_BAG, 920.0, 4.5],
        'ST-01B' => [ContainerTypeEnum::IBC, 950.0, 60.0],
        'ST-02A' => [ContainerTypeEnum::PALLET, 1200.0, 25.0],
        'ST-03C' => [ContainerTypeEnum::BULK_PILE, 8500.0, null],
    ];

    public function run(): void
    {
        $warehouse = Warehouse::query()->where('code', WarehouseSeeder::DEMO_CODE)->first();
        if ($warehouse === null || $warehouse->stockUnits()->exists()) {
            return;
        }

        $create = app(CreateStockUnitAction::class);

        foreach (self::UNITS as $locationCode => [$container, $netKg, $tareKg]) {
            $location = Location::query()
                ->where('code', $locationCode)
                ->whereIn('area_id', Area::query()->select('id')->where('warehouse_id', $warehouse->id))
                ->first();

            $create->execute(new StockUnitData(
                warehouseId: $warehouse->id,
                containerType: $container,
                netWeightKg: $netKg,
                tareWeightKg: $tareKg,
                locationId: $location?->id,
            ));
        }
    }
}
