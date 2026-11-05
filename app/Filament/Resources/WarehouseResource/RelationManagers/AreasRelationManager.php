<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\WarehouseResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Arr;
use Modules\Warehouse\Filament\Resources\AreaResource\Schemas\AreaForm;
use Modules\Warehouse\Filament\Resources\AreaResource\Tables\AreasTable;
use Modules\Xot\Filament\Resources\RelationManagers\XotBaseRelationManager;

/**
 * Aree del magazzino: stesso form e stesse colonne di AreaResource, senza il magazzino (è l'owner).
 */
class AreasRelationManager extends XotBaseRelationManager
{
    protected static string $relationship = 'areas';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return array<string, Component>
     */
    #[\Override]
    public function getFormSchema(): array
    {
        /** @var array<string, Component> $schema */
        $schema = Arr::except(app(AreaForm::class)->getFormSchema(), ['warehouse_id']);

        return $schema;
    }

    /**
     * @return array<string, Column>
     */
    #[\Override]
    public function getTableColumns(): array
    {
        /** @var array<string, Column> $columns */
        $columns = Arr::except(app(AreasTable::class)->getTableColumns(), ['warehouse.code']);

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
