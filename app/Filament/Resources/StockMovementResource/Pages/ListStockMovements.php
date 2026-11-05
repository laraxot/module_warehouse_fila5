<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockMovementResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Modules\Warehouse\Filament\Resources\StockMovementResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListStockMovements extends XotBaseListRecords
{
    protected static string $resource = StockMovementResource::class;

    /**
     * Nessun "crea": i movimenti li registrano solo le Actions di dominio.
     *
     * @return array<string, Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
