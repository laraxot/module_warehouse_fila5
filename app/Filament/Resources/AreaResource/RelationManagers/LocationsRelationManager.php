<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\AreaResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Modules\Labeling\Filament\Actions\PrintLabelTableAction;
use Modules\Warehouse\Filament\Resources\LocationResource\Schemas\LocationForm;
use Modules\Warehouse\Filament\Resources\LocationResource\Tables\LocationsTable;
use Modules\Warehouse\Models\Location;
use Modules\Xot\Filament\Resources\RelationManagers\XotBaseRelationManager;

/**
 * Posizioni dell'area: stesso form e stesse colonne di LocationResource, senza l'area (è l'owner).
 */
class LocationsRelationManager extends XotBaseRelationManager
{
    protected static string $relationship = 'locations';

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * @return array<string, Component>
     */
    #[\Override]
    public function getFormSchema(): array
    {
        /** @var array<string, Component> $schema */
        $schema = Arr::except(app(LocationForm::class)->getFormSchema(), ['area_id']);

        return $schema;
    }

    /**
     * Carico per riga in un'unica query (`withLoadKg()`) invece di una SUM per riga.
     */
    #[\Override]
    public function table(Table $table): Table
    {
        return parent::table($table)->modifyQueryUsing(static fn (Builder $query): Builder => Location::applyLoadKg($query));
    }

    /**
     * @return array<string, Column>
     */
    #[\Override]
    public function getTableColumns(): array
    {
        /** @var array<string, Column> $columns */
        $columns = Arr::except(app(LocationsTable::class)->getTableColumns(), ['area.warehouse.code', 'area.code']);

        return $columns;
    }

    /**
     * @return array<string, Action>
     */
    #[\Override]
    public function getTableHeaderActions(): array
    {
        return [
            'create' => CreateAction::make(),
        ];
    }

    /**
     * @return array<string, Action>
     */
    #[\Override]
    public function getTableActions(): array
    {
        return [
            'print_label' => PrintLabelTableAction::make(),
            'edit' => EditAction::make(),
            'delete' => DeleteAction::make(),
        ];
    }

    #[\Override]
    public function getTableBulkActions(): array
    {
        return [];
    }
}
