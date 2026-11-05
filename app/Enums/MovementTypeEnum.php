<?php

declare(strict_types=1);

namespace Modules\Warehouse\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Tipo di movimento di magazzino.
 *
 * `quantity_kg` di un movimento è sempre positivo, tranne per `adjustment` dove è la
 * differenza con segno (nuovo peso - peso precedente).
 *
 * Etichetta, colore e icona: Modules/Warehouse/lang/{locale}/movement_type_enum.php.
 */
enum MovementTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case RECEIPT = 'receipt';
    case PUTAWAY = 'putaway';
    case TRANSFER = 'transfer';
    case CONSUMPTION = 'consumption';
    case PRODUCTION = 'production';
    case SHIPMENT = 'shipment';
    case ADJUSTMENT = 'adjustment';
    case VOID = 'void';

    /** Movimenti che fanno nascere una unità (primo movimento della sua storia). */
    public function isOrigin(): bool
    {
        return \in_array($this, [self::RECEIPT, self::PRODUCTION], true);
    }

    /** Movimenti che spostano l'unità senza cambiarne il peso. */
    public function isPlacement(): bool
    {
        return \in_array($this, [self::PUTAWAY, self::TRANSFER], true);
    }

    /** Movimenti dopo i quali l'unità non è più in giacenza. */
    public function isExit(): bool
    {
        return \in_array($this, [self::SHIPMENT, self::VOID], true);
    }

    /**
     * Movimenti che modificano il peso netto registrato sull'unità.
     * Spedizione e annullamento azzerano la giacenza ma l'unità conserva il suo ultimo peso fisico.
     */
    public function changesUnitWeight(): bool
    {
        return \in_array($this, [self::CONSUMPTION, self::ADJUSTMENT], true);
    }

    /**
     * Peso in giacenza dopo il movimento, dato il peso precedente e la quantità del movimento.
     */
    public function weightAfter(float $weightBeforeKg, float $quantityKg): float
    {
        $after = match ($this) {
            self::RECEIPT, self::PRODUCTION => $weightBeforeKg + $quantityKg,
            self::PUTAWAY, self::TRANSFER => $weightBeforeKg,
            self::CONSUMPTION => $weightBeforeKg - $quantityKg,
            self::ADJUSTMENT => $weightBeforeKg + $quantityKg,
            self::SHIPMENT, self::VOID => 0.0,
        };

        return round($after, 3);
    }
}
