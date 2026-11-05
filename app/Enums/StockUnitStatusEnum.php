<?php

declare(strict_types=1);

namespace Modules\Warehouse\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Stato di una unità di stoccaggio.
 *
 * - pending: ricevuta/prodotta, non ancora posizionata;
 * - stored: in una posizione del magazzino;
 * - reserved: in posizione ma impegnata (es. per un'uscita);
 * - in_process: prelevata per una lavorazione;
 * - consumed / shipped / voided: stati finali, l'unità non è più in giacenza.
 *
 * Etichetta, colore e icona: Modules/Warehouse/lang/{locale}/stock_unit_status_enum.php.
 */
enum StockUnitStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case PENDING = 'pending';
    case STORED = 'stored';
    case RESERVED = 'reserved';
    case IN_PROCESS = 'in_process';
    case CONSUMED = 'consumed';
    case SHIPPED = 'shipped';
    case VOIDED = 'voided';

    /**
     * Stati che occupano capacità in magazzino (usati dal calcolo del carico di aree e posizioni).
     *
     * @return list<self>
     */
    public static function occupying(): array
    {
        return [self::PENDING, self::STORED, self::RESERVED];
    }

    public function isOccupying(): bool
    {
        return \in_array($this, self::occupying(), true);
    }

    /** L'unità esiste ancora fisicamente in magazzino (anche se in lavorazione). */
    public function isInStock(): bool
    {
        return ! $this->isFinal();
    }

    public function isFinal(): bool
    {
        return \in_array($this, [self::CONSUMED, self::SHIPPED, self::VOIDED], true);
    }

    public function canTransitionTo(self $to): bool
    {
        return \in_array($to, $this->allowedTransitions(), true);
    }

    /**
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::STORED, self::RESERVED, self::IN_PROCESS, self::CONSUMED, self::SHIPPED, self::VOIDED],
            self::STORED => [self::STORED, self::RESERVED, self::IN_PROCESS, self::CONSUMED, self::SHIPPED, self::VOIDED],
            self::RESERVED => [self::STORED, self::RESERVED, self::IN_PROCESS, self::CONSUMED, self::SHIPPED, self::VOIDED],
            self::IN_PROCESS => [self::STORED, self::CONSUMED, self::VOIDED],
            self::CONSUMED, self::SHIPPED, self::VOIDED => [],
        };
    }
}
