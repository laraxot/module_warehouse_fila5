<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\AreaResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Modules\Warehouse\Actions\GetAreaOccupancyAction;
use Modules\Warehouse\Enums\AreaTypeEnum;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class AreasTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'warehouse.code' => TextColumn::make('warehouse.code')->sortable()->searchable(),
            'code' => TextColumn::make('code')->searchable()->sortable(),
            'name' => TextColumn::make('name')->searchable()->sortable(),
            'type' => TextColumn::make('type')->badge()->sortable(),
            'max_weight_kg' => TextColumn::make('max_weight_kg')->numeric(3)->sortable(),
            'load_kg' => TextColumn::make('load_kg')
                ->state(static fn (Area $record): float => app(GetAreaOccupancyAction::class)->execute($record)->loadKg)
                ->numeric(3),
            'occupancy_pct' => TextColumn::make('occupancy_pct')
                ->state(static fn (Area $record): ?float => app(GetAreaOccupancyAction::class)->execute($record)->percentage())
                ->numeric(1)
                ->badge()
                ->color(static fn (Area $record): string => app(GetAreaOccupancyAction::class)->execute($record)->level()->getColor()),
            'locations_count' => TextColumn::make('locations_count')->counts('locations')->toggleable(),
            'color' => ColorColumn::make('color')->toggleable(),
            'is_active' => IconColumn::make('is_active')->boolean(),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            'warehouse_id' => SelectFilter::make('warehouse_id')->relationship('warehouse', 'name'),
            'type' => SelectFilter::make('type')->options(AreaTypeEnum::class)->multiple(),
            'is_active' => TernaryFilter::make('is_active'),
        ];
    }

    public function getTableActions(): array
    {
        return [
            'edit' => EditAction::make(),
            'delete' => DeleteAction::make(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [];
    }
}
