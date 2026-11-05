<?php

declare(strict_types=1);

namespace Modules\Warehouse\Datas;

use Spatie\LaravelData\Data;

/**
 * Verifica pura di portata: carico attuale + peso in arrivo rispetto al massimo (null = illimitato).
 *
 * `alreadyIncludedKg` è la parte del peso in arrivo già compresa nel carico attuale
 * (unità che si sposta all'interno della stessa posizione/area).
 */
final class CapacityCheckData extends Data
{
    /** Tolleranza per gli arrotondamenti dei decimali (kg). */
    public const float EPSILON_KG = 0.0005;

    public function __construct(
        public readonly ?float $maxKg,
        public readonly float $loadKg,
        public readonly float $incomingKg,
        public readonly float $alreadyIncludedKg = 0.0,
    ) {}

    public function projectedKg(): float
    {
        return round($this->loadKg - $this->alreadyIncludedKg + $this->incomingKg, 3);
    }

    public function exceeds(): bool
    {
        if ($this->maxKg === null) {
            return false;
        }

        return $this->projectedKg() > $this->maxKg + self::EPSILON_KG;
    }
}
