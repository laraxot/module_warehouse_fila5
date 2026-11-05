<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\LocationResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Warehouse\Actions\GetLocationLoadKgAction;
use Modules\Warehouse\Models\Location;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class LocationInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'area.warehouse.name' => TextEntry::make('area.warehouse.name'),
            'area.name' => TextEntry::make('area.name'),
            'code' => TextEntry::make('code'),
            'name' => TextEntry::make('name'),
            'x' => TextEntry::make('x')->numeric(2),
            'y' => TextEntry::make('y')->numeric(2),
            'max_weight_kg' => TextEntry::make('max_weight_kg')->numeric(3),
            'load_kg' => TextEntry::make('load_kg')
                ->state(static fn (Location $record): float => app(GetLocationLoadKgAction::class)->execute($record))
                ->numeric(3),
            'is_active' => IconEntry::make('is_active')->boolean(),
        ];
    }
}
