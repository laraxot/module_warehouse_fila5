<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;

/**
 * Factory per test di lettura: in produzione i movimenti li scrive solo RecordStockMovementAction.
 *
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    /** @var class-string<StockMovement> */
    protected $model = StockMovement::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kg = $this->faker->randomFloat(3, 50, 1500);

        return [
            'stock_unit_id' => StockUnit::factory(),
            'type' => MovementTypeEnum::RECEIPT,
            'quantity_kg' => $kg,
            'weight_before_kg' => 0,
            'weight_after_kg' => $kg,
            'from_location_id' => null,
            'to_location_id' => null,
            'occurred_at' => CarbonImmutable::now(),
            'notes' => null,
        ];
    }
}
