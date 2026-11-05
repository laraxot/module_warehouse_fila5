<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources;

use Modules\Warehouse\Filament\Resources\LocationResource\Pages\CreateLocation;
use Modules\Warehouse\Filament\Resources\LocationResource\Pages\EditLocation;
use Modules\Warehouse\Filament\Resources\LocationResource\Pages\ListLocations;
use Modules\Warehouse\Models\Location;
use Modules\Xot\Filament\Resources\XotBaseResource;

class LocationResource extends XotBaseResource
{
    protected static ?string $model = Location::class;

    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'edit' => EditLocation::route('/{record}/edit'),
        ];
    }
}
