<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockMovementResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Warehouse\Models\StockMovement;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class StockMovementInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'occurred_at' => TextEntry::make('occurred_at')->dateTime(),
            'stockUnit.code' => TextEntry::make('stockUnit.code'),
            'type' => TextEntry::make('type')->badge(),
            'quantity_kg' => TextEntry::make('quantity_kg')->numeric(3),
            'weight_before_kg' => TextEntry::make('weight_before_kg')->numeric(3),
            'weight_after_kg' => TextEntry::make('weight_after_kg')->numeric(3),
            'fromLocation.code' => TextEntry::make('fromLocation.code'),
            'toLocation.code' => TextEntry::make('toLocation.code'),
            'reference_type' => TextEntry::make('reference_type')
                ->formatStateUsing(static fn (?string $state, StockMovement $record): string => $state === null ? '' : class_basename($state).' #'.$record->reference_id),
            'notes' => TextEntry::make('notes')->columnSpanFull(),
        ];
    }
}
