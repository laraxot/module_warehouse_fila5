<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /** @var class-string<Location> */
    protected $model = Location::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area_id' => Area::factory(),
            'code' => $this->faker->unique()->bothify('L-###??'),
            'name' => null,
            'x' => $this->faker->randomFloat(2, 1, 90),
            'y' => $this->faker->randomFloat(2, 1, 50),
            'max_weight_kg' => null,
            'is_active' => true,
        ];
    }

    public function withMaxWeight(?float $kg): static
    {
        return $this->state(fn (array $_attributes): array => ['max_weight_kg' => $kg]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $_attributes): array => ['is_active' => false]);
    }
}
