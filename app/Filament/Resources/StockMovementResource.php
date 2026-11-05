<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources;

use Modules\Warehouse\Filament\Resources\StockMovementResource\Pages\ListStockMovements;
use Modules\Warehouse\Models\StockMovement;
use Modules\Xot\Filament\Resources\XotBaseResource;

/**
 * Registro dei movimenti in sola lettura: nessuna pagina di creazione o modifica.
 */
class StockMovementResource extends XotBaseResource
{
    protected static ?string $model = StockMovement::class;

    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
        ];
    }
}
