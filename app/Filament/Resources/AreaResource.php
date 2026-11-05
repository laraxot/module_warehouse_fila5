<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources;

use Modules\Warehouse\Filament\Resources\AreaResource\Pages\CreateArea;
use Modules\Warehouse\Filament\Resources\AreaResource\Pages\EditArea;
use Modules\Warehouse\Filament\Resources\AreaResource\Pages\ListAreas;
use Modules\Warehouse\Filament\Resources\AreaResource\RelationManagers\LocationsRelationManager;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Filament\Resources\XotBaseResource;

class AreaResource extends XotBaseResource
{
    protected static ?string $model = Area::class;

    #[\Override]
    public static function getRelations(): array
    {
        return [
            LocationsRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListAreas::route('/'),
            'create' => CreateArea::route('/create'),
            'edit' => EditArea::route('/{record}/edit'),
        ];
    }
}
