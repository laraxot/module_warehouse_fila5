<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\WarehouseResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class WarehouseInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'code' => TextEntry::make('code'),
            'name' => TextEntry::make('name'),
            'description' => TextEntry::make('description')->columnSpanFull(),
            'map_width_m' => TextEntry::make('map_width_m')->numeric(2),
            'map_height_m' => TextEntry::make('map_height_m')->numeric(2),
            'is_active' => IconEntry::make('is_active')->boolean(),
            'map' => SpatieMediaLibraryImageEntry::make('map')->collection(Warehouse::MAP_COLLECTION)->columnSpanFull(),
        ];
    }
}
