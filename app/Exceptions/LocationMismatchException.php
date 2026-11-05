<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * La posizione non è utilizzabile per l'unità: appartiene a un altro magazzino oppure è disattivata.
 */
class LocationMismatchException extends WarehouseException
{
    public static function otherWarehouse(StockUnit $unit, Location $location): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.location.other_warehouse', [
            'code' => $unit->code,
            'location' => $location->code,
        ])));
    }

    public static function inactive(Location $location): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.location.inactive', ['location' => $location->code])));
    }

    public static function sameLocation(StockUnit $unit, Location $location): self
    {
        return new self(SafeStringCastAction::cast(__('warehouse::exceptions.location.same_location', [
            'code' => $unit->code,
            'location' => $location->code,
        ])));
    }
}
