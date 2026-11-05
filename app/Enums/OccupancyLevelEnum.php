<?php

declare(strict_types=1);

namespace Modules\Warehouse\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Fascia di occupazione di un'area o di una posizione (colore della mappa e del widget).
 *
 * Il colore (`values.{value}.color` nel file lang) è un colore Filament (`success`, `warning`, ...):
 * la mappa SVG lo usa tramite le variabili CSS del pannello (`var(--success-500)`).
 */
enum OccupancyLevelEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case EMPTY = 'empty';
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case FULL = 'full';
    case UNBOUNDED = 'unbounded';

    public static function fromPercentage(?float $percentage): self
    {
        return match (true) {
            $percentage === null => self::UNBOUNDED,
            $percentage <= 0.0 => self::EMPTY,
            $percentage < 50.0 => self::LOW,
            $percentage < 80.0 => self::MEDIUM,
            $percentage < 100.0 => self::HIGH,
            default => self::FULL,
        };
    }
}
