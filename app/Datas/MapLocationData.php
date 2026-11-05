<?php

declare(strict_types=1);

namespace Modules\Warehouse\Datas;

use Spatie\LaravelData\Data;

/**
 * Una posizione sulla mappa SVG (punto in metri).
 */
final class MapLocationData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $areaId,
        public readonly string $code,
        public readonly float $x,
        public readonly float $y,
        public readonly OccupancyData $occupancy,
    ) {}
}
