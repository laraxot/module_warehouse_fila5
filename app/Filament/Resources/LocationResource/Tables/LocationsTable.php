<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\LocationResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Modules\Labeling\Filament\Actions\PrintLabelTableAction;
use Modules\Warehouse\Actions\GetLocationLoadKgAction;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class LocationsTable extends XotBaseResourceTable
{
    /**
     * Carico per riga in un'unica query (`withLoadKg()`) invece di una SUM per riga.
     */
    public function table(Table $table): Table
    {
        return parent::table($table)->modifyQueryUsing(static fn (Builder $query): Builder => Location::applyLoadKg($query));
    }

    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'area.warehouse.code' => TextColumn::make('area.warehouse.code')->sortable(),
            'area.code' => TextColumn::make('area.code')->sortable()->searchable(),
            'code' => TextColumn::make('code')->searchable()->sortable(),
            'name' => TextColumn::make('name')->searchable()->toggleable(),
            'x' => TextColumn::make('x')->numeric(2)->toggleable(isToggledHiddenByDefault: true),
            'y' => TextColumn::make('y')->numeric(2)->toggleable(isToggledHiddenByDefault: true),
            'max_weight_kg' => TextColumn::make('max_weight_kg')->numeric(3)->sortable(),
            'load_kg' => TextColumn::make('load_kg')
                ->state(static fn (Location $record): float => app(GetLocationLoadKgAction::class)->execute($record))
                ->numeric(3),
            'stock_units_count' => TextColumn::make('stock_units_count')
                ->counts(['stockUnits' => static function (Builder $query): void {
                    StockUnit::constrainToOccupying($query);
                }]),
            'is_active' => IconColumn::make('is_active')->boolean(),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            'area_id' => SelectFilter::make('area_id')->relationship('area', 'name')->searchable()->preload(),
            'is_active' => TernaryFilter::make('is_active'),
        ];
    }

    public function getTableActions(): array
    {
        return [
            'print_label' => PrintLabelTableAction::make(),
            'edit' => EditAction::make(),
            'delete' => DeleteAction::make(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [];
    }
}
