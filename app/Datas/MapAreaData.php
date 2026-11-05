<?php

declare(strict_types=1);

namespace Modules\Warehouse\Datas;

use Modules\Warehouse\Enums\AreaTypeEnum;
use Spatie\LaravelData\Data;

/**
 * Un'area pronta per la mappa SVG: coordinate già in unità SVG (1 = 1 metro).
 */
final class MapAreaData extends Data
{
    /**
     * @param  list<array{0: float, 1: float}>  $polygon
     */
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $name,
        public readonly AreaTypeEnum $type,
        public readonly array $polygon,
        public readonly ?string $color,
        public readonly OccupancyData $occupancy,
        public readonly int $unitsCount,
    ) {}

    /** Attributo `points` del <polygon> SVG. */
    public function svgPoints(): string
    {
        return implode(' ', array_map(
            static fn (array $point): string => self::number($point[0]).','.self::number($point[1]),
            $this->polygon,
        ));
    }

    /**
     * Centroide (media dei vertici) per posizionare l'etichetta.
     *
     * @return array{0: float, 1: float}
     */
    public function labelPoint(): array
    {
        $count = \count($this->polygon);
        if ($count === 0) {
            return [0.0, 0.0];
        }

        $x = array_sum(array_column($this->polygon, 0)) / $count;
        $y = array_sum(array_column($this->polygon, 1)) / $count;

        return [round($x, 2), round($y, 2)];
    }

    private static function number(float $value): string
    {
        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }
}
