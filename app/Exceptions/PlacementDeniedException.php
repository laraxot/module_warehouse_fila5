<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;

/**
 * Un PlacementGuard ha rifiutato il posizionamento di una unità in una posizione.
 * Il messaggio è il motivo restituito dal guard (già tradotto).
 */
class PlacementDeniedException extends WarehouseException
{
    public function __construct(
        string $reason,
        public readonly ?int $stockUnitId = null,
        public readonly ?int $locationId = null,
    ) {
        parent::__construct($reason);
    }

    public static function because(string $reason, StockUnit $unit, Location $location): self
    {
        return new self($reason, $unit->exists ? $unit->id : null, $location->id);
    }
}
