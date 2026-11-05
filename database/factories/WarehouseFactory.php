<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Models\Warehouse;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    /** @var class-string<Warehouse> */
    protected $model = Warehouse::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('WH-###??'),
            'name' => $this->faker->company(),
            'description' => $this->faker->optional()->sentence(),
            'map_width_m' => 100,
            'map_height_m' => 60,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $_attributes): array => ['is_active' => false]);
    }
}
