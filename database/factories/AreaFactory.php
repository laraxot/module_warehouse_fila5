<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Warehouse\Enums\AreaTypeEnum;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Warehouse;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    /** @var class-string<Area> */
    protected $model = Area::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $x = $this->faker->numberBetween(0, 80);
        $y = $this->faker->numberBetween(0, 40);

        return [
            'warehouse_id' => Warehouse::factory(),
            'code' => $this->faker->unique()->bothify('A##??'),
            'name' => $this->faker->words(2, true),
            'type' => AreaTypeEnum::STORAGE,
            'max_weight_kg' => null,
            'map_geometry' => [[$x, $y], [$x + 20, $y], [$x + 20, $y + 15], [$x, $y + 15]],
            'color' => $this->faker->hexColor(),
            'is_active' => true,
        ];
    }

    public function ofType(AreaTypeEnum $type): static
    {
        return $this->state(fn (array $_attributes): array => ['type' => $type]);
    }

    public function withMaxWeight(?float $kg): static
    {
        return $this->state(fn (array $_attributes): array => ['max_weight_kg' => $kg]);
    }
}
