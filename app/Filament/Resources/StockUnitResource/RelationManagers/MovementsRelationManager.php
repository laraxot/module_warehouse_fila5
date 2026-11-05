<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Arr;
use Modules\Warehouse\Filament\Resources\StockMovementResource\Tables\StockMovementsTable;
use Modules\Xot\Filament\Resources\RelationManagers\XotBaseRelationManager;

/**
 * Storia dei movimenti dell'unità, in sola lettura (registro immutabile).
 */
class MovementsRelationManager extends XotBaseRelationManager
{
    protected static string $relationship = 'movements';

    protected static ?string $recordTitleAttribute = 'id';

    #[\Override]
    public function isReadOnly(): bool
    {
        return true;
    }

    /**
     * @return array<string, Component>
     */
    #[\Override]
    public function getFormSchema(): array
    {
        return [];
    }

    /**
     * @return array<string, Column>
     */
    #[\Override]
    public function getTableColumns(): array
    {
        /** @var array<string, Column> $columns */
        $columns = Arr::except(app(StockMovementsTable::class)->getTableColumns(), ['stockUnit.code']);

        return $columns;
    }

    /**
     * @return array<string, Action>
     */
    #[\Override]
    public function getTableHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<string, Action>
     */
    #[\Override]
    public function getTableActions(): array
    {
        return [];
    }

    #[\Override]
    public function getTableBulkActions(): array
    {
        return [];
    }
}
