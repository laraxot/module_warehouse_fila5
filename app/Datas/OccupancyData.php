<?php

declare(strict_types=1);

namespace Modules\Warehouse\Datas;

use Modules\Warehouse\Enums\OccupancyLevelEnum;
use Spatie\LaravelData\Data;

/**
 * Occupazione di un'area o posizione: carico, portata e percentuale (null se portata illimitata).
 */
final class OccupancyData extends Data
{
    public function __construct(
        public readonly float $loadKg,
        public readonly ?float $maxKg,
    ) {}

    public function percentage(): ?float
    {
        if ($this->maxKg === null || $this->maxKg <= 0.0) {
            return null;
        }

        return round($this->loadKg / $this->maxKg * 100, 1);
    }

    public function level(): OccupancyLevelEnum
    {
        if ($this->maxKg === null && $this->loadKg <= 0.0) {
            return OccupancyLevelEnum::EMPTY;
        }

        return OccupancyLevelEnum::fromPercentage($this->percentage());
    }
}
