<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class StockUnitInfolist extends XotBaseResourceInfolist
{
    public function getInfolistColumns(): int
    {
        return 3;
    }

    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'code' => TextEntry::make('code')->copyable()->weight('bold'),
            'status' => TextEntry::make('status')->badge(),
            'container_type' => TextEntry::make('container_type')->badge(),
            'warehouse.name' => TextEntry::make('warehouse.name'),
            'location.area.name' => TextEntry::make('location.area.name'),
            'location.code' => TextEntry::make('location.code'),
            'net_weight_kg' => TextEntry::make('net_weight_kg')->numeric(3),
            'tare_weight_kg' => TextEntry::make('tare_weight_kg')->numeric(3),
            'gross_weight_kg' => TextEntry::make('gross_weight_kg')
                ->state(static fn (StockUnit $record): float => $record->grossWeightKg())
                ->numeric(3),
            'subject_type' => TextEntry::make('subject_type')
                ->formatStateUsing(static fn (?string $state): string => $state === null ? '' : class_basename($state)),
            'subject_id' => TextEntry::make('subject_id'),
            'received_at' => TextEntry::make('received_at')->dateTime(),
            'notes' => TextEntry::make('notes')->columnSpanFull(),
        ];
    }
}
