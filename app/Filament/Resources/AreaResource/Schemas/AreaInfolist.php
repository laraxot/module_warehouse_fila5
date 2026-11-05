<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\AreaResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Warehouse\Actions\GetAreaLoadKgAction;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class AreaInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'warehouse.name' => TextEntry::make('warehouse.name'),
            'code' => TextEntry::make('code'),
            'name' => TextEntry::make('name'),
            'type' => TextEntry::make('type')->badge(),
            'max_weight_kg' => TextEntry::make('max_weight_kg')->numeric(3),
            'load_kg' => TextEntry::make('load_kg')
                ->state(static fn (Area $record): float => app(GetAreaLoadKgAction::class)->execute($record))
                ->numeric(3),
            'color' => ColorEntry::make('color'),
            'is_active' => IconEntry::make('is_active')->boolean(),
        ];
    }
}
