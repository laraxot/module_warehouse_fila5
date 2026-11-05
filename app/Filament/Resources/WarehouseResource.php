<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources;

use Modules\Warehouse\Filament\Resources\WarehouseResource\Pages\CreateWarehouse;
use Modules\Warehouse\Filament\Resources\WarehouseResource\Pages\EditWarehouse;
use Modules\Warehouse\Filament\Resources\WarehouseResource\Pages\ListWarehouses;
use Modules\Warehouse\Filament\Resources\WarehouseResource\RelationManagers\AreasRelationManager;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Filament\Resources\XotBaseResource;

class WarehouseResource extends XotBaseResource
{
    protected static ?string $model = Warehouse::class;

    #[\Override]
    public static function getRelations(): array
    {
        return [
            AreasRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListWarehouses::route('/'),
            'create' => CreateWarehouse::route('/create'),
            'edit' => EditWarehouse::route('/{record}/edit'),
        ];
    }
}
