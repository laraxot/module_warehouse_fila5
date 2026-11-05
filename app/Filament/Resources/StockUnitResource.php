<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources;

use Modules\Warehouse\Filament\Resources\StockUnitResource\Pages\CreateStockUnit;
use Modules\Warehouse\Filament\Resources\StockUnitResource\Pages\EditStockUnit;
use Modules\Warehouse\Filament\Resources\StockUnitResource\Pages\ListStockUnits;
use Modules\Warehouse\Filament\Resources\StockUnitResource\Pages\ViewStockUnit;
use Modules\Warehouse\Filament\Resources\StockUnitResource\RelationManagers\MovementsRelationManager;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Filament\Resources\XotBaseResource;

class StockUnitResource extends XotBaseResource
{
    protected static ?string $model = StockUnit::class;

    #[\Override]
    public static function getRelations(): array
    {
        return [
            MovementsRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListStockUnits::route('/'),
            'create' => CreateStockUnit::route('/create'),
            'view' => ViewStockUnit::route('/{record}'),
            'edit' => EditStockUnit::route('/{record}/edit'),
        ];
    }
}
