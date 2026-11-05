<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Modules\Labeling\Filament\Actions\PrintLabelTableAction;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class StockUnitsTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'code' => TextColumn::make('code')->searchable()->sortable()->copyable(),
            'status' => TextColumn::make('status')->badge()->sortable(),
            'container_type' => TextColumn::make('container_type')->badge(),
            'warehouse.code' => TextColumn::make('warehouse.code')->sortable()->toggleable(),
            'location.code' => TextColumn::make('location.code')->searchable()->sortable(),
            'net_weight_kg' => TextColumn::make('net_weight_kg')->numeric(3)->sortable(),
            'subject_type' => TextColumn::make('subject_type')
                ->formatStateUsing(static fn (?string $state): string => $state === null ? '' : class_basename($state))
                ->toggleable(isToggledHiddenByDefault: true),
            'received_at' => TextColumn::make('received_at')->dateTime()->sortable(),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            'status' => SelectFilter::make('status')->options(StockUnitStatusEnum::class)->multiple(),
            'container_type' => SelectFilter::make('container_type')->options(ContainerTypeEnum::class)->multiple(),
            'warehouse_id' => SelectFilter::make('warehouse_id')->relationship('warehouse', 'name'),
            'location_id' => SelectFilter::make('location_id')->relationship('location', 'code')->searchable(),
            'in_stock' => Filter::make('in_stock')
                ->query(static function (Builder $query): Builder {
                    $query->whereNotIn('status', [
                        StockUnitStatusEnum::CONSUMED->value,
                        StockUnitStatusEnum::SHIPPED->value,
                        StockUnitStatusEnum::VOIDED->value,
                    ]);

                    return $query;
                })
                ->default(),
        ];
    }

    public function getTableActions(): array
    {
        return [
            'view' => ViewAction::make(),
            'print_label' => PrintLabelTableAction::make()
                ->visible(static fn (StockUnit $record): bool => $record->status->isInStock()),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'received_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
