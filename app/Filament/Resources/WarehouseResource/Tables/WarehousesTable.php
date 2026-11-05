<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\WarehouseResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class WarehousesTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'code' => TextColumn::make('code')->searchable()->sortable(),
            'name' => TextColumn::make('name')->searchable()->sortable(),
            'areas_count' => TextColumn::make('areas_count')->counts('areas')->sortable(),
            'stock_units_count' => TextColumn::make('stock_units_count')->counts('stockUnits')->toggleable(),
            'is_active' => IconColumn::make('is_active')->boolean(),
            'updated_at' => TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public function getTableFilters(): array
    {
        return [
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
