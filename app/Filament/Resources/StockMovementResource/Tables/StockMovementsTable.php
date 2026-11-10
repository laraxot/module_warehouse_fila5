<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockMovementResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Filament\Resources\StockUnitResource;
use Modules\Warehouse\Models\StockMovement;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

class StockMovementsTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'occurred_at' => TextColumn::make('occurred_at')->dateTime()->sortable(),
            'stockUnit.code' => TextColumn::make('stockUnit.code')
                ->searchable()
                ->url(static fn (StockMovement $record): string => StockUnitResource::getUrl('view', ['record' => $record->stock_unit_id])),
            'type' => TextColumn::make('type')->badge()->sortable(),
            'quantity_kg' => TextColumn::make('quantity_kg')->numeric(3)->sortable(),
            'weight_before_kg' => TextColumn::make('weight_before_kg')->numeric(3)->toggleable(),
            'weight_after_kg' => TextColumn::make('weight_after_kg')->numeric(3)->toggleable(),
            'fromLocation.code' => TextColumn::make('fromLocation.code'),
            'toLocation.code' => TextColumn::make('toLocation.code'),
            'reference_type' => TextColumn::make('reference_type')
                ->formatStateUsing(static fn (?string $state, StockMovement $record): string => $state === null ? '' : class_basename($state).' #'.$record->reference_id)
                ->toggleable(isToggledHiddenByDefault: true),
            'notes' => TextColumn::make('notes')->limit(60)->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            'type' => SelectFilter::make('type')->options(MovementTypeEnum::class)->multiple(),
            'stock_unit_id' => SelectFilter::make('stock_unit_id')->relationship('stockUnit', 'code')->searchable(),
            // form(): da Filament 5.11.2 (la versione che la CI installa) schema() sui filtri e' deprecato.
            'occurred_at' => Filter::make('occurred_at')
                ->form([
                    DatePicker::make('occurred_from'),
                    DatePicker::make('occurred_until'),
                ])
                ->query(static function (Builder $query, array $data): Builder {
                    $from = $data['occurred_from'] ?? null;
                    $until = $data['occurred_until'] ?? null;

                    if (\is_string($from) && $from !== '') {
                        $query->whereDate('occurred_at', '>=', $from);
                    }

                    if (\is_string($until) && $until !== '') {
                        $query->whereDate('occurred_at', '<=', $until);
                    }

                    return $query;
                }),
        ];
    }

    public function getTableActions(): array
    {
        return [
            'view' => ViewAction::make(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'occurred_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
