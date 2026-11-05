<?php

declare(strict_types=1);

namespace Modules\Warehouse\Datas;

use Spatie\LaravelData\Data;

/**
 * Tutto ciò che serve a disegnare la mappa di un magazzino lato server.
 */
final class WarehouseMapData extends Data
{
    /**
     * @param  list<MapAreaData>  $areas
     * @param  list<MapLocationData>  $locations
     */
    public function __construct(
        public readonly int $warehouseId,
        public readonly float $widthM,
        public readonly float $heightM,
        public readonly ?string $backgroundUrl,
        public readonly array $areas,
        public readonly array $locations,
    ) {}

    public function viewBox(): string
    {
        return '0 0 '.$this->widthM.' '.$this->heightM;
    }

    /** Raggio dei punti posizione, proporzionale alla planimetria. */
    public function pointRadius(): float
    {
        return round(max(min($this->widthM, $this->heightM) / 120, 0.15), 3);
    }

    /** Dimensione del testo delle etichette area, proporzionale alla planimetria. */
    public function fontSize(): float
    {
        return round(max(min($this->widthM, $this->heightM) / 40, 0.4), 3);
    }
}
