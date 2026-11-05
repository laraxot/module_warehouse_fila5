<?php

declare(strict_types=1);

namespace Modules\Warehouse\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Funzione di un'area del magazzino nel flusso fisico della merce.
 *
 * Etichetta, colore e icona: Modules/Warehouse/lang/{locale}/area_type_enum.php.
 */
enum AreaTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case INBOUND = 'inbound';
    case QUARANTINE = 'quarantine';
    case STORAGE = 'storage';
    case PROCESSING = 'processing';
    case OUTBOUND = 'outbound';
}
