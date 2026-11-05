<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;

/**
 * Factory per test e tabelle: NON genera movimenti. Le unità "vere" si creano con
 * CreateStockUnitAction. Il codice usa il prefisso `FX-` per non interferire con il
 * progressivo `SU-{anno}-…` del generatore.
 *
 * @extends Factory<StockUnit>
 */
class StockUnitFactory extends Factory
{
    /** @var class-string<StockUnit> */
    protected $model = StockUnit::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'code' => $this->faker->unique()->bothify('FX-######??'),
            'container_type' => $this->faker->randomElement(ContainerTypeEnum::cases()),
            'status' => StockUnitStatusEnum::PENDING,
            'location_id' => null,
            'net_weight_kg' => $this->faker->randomFloat(3, 50, 1500),
            'tare_weight_kg' => $this->faker->optional()->randomFloat(3, 5, 80),
            'received_at' => CarbonImmutable::now(),
            'notes' => null,
        ];
    }
}
