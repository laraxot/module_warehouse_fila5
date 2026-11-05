<?php

declare(strict_types=1);

namespace Modules\Warehouse\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Tipo fisico del contenitore di una unità di stoccaggio.
 *
 * Etichetta, colore e icona: Modules/Warehouse/lang/{locale}/container_type_enum.php.
 */
enum ContainerTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case BIG_BAG = 'big_bag';
    case DRUM = 'drum';
    case IBC = 'ibc';
    case SKIP = 'skip';
    case ROLL_OFF = 'roll_off';
    case PALLET = 'pallet';
    case BULK_PILE = 'bulk_pile';
    case TANK = 'tank';
    case BOX = 'box';
    case OTHER = 'other';
}
