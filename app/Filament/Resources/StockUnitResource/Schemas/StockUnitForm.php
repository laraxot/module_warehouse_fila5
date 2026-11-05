<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * In creazione: magazzino, contenitore, pesi e posizione iniziale (CreateStockUnitAction).
 * In modifica: solo contenitore, tara e note. Peso netto, posizione e stato cambiano solo
 * con le azioni di dominio (sposta, rettifica, annulla) che registrano i movimenti.
 */
class StockUnitForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'code' => TextInput::make('code')
                ->disabled()
                ->dehydrated(false)
                ->visibleOn('edit'),
            'warehouse_id' => Select::make('warehouse_id')
                ->options(static fn (): array => Warehouse::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get()
                    ->mapWithKeys(static fn (Warehouse $warehouse): array => [$warehouse->id => $warehouse->name])
                    ->all())
                ->required()
                ->live()
                ->afterStateUpdated(static fn (Set $set): mixed => $set('location_id', null))
                ->visibleOn('create'),
            'container_type' => Select::make('container_type')
                ->options(ContainerTypeEnum::class)
                ->required(),
            'net_weight_kg' => TextInput::make('net_weight_kg')
                ->numeric()
                ->minValue(0.001)
                ->step(0.001)
                ->required()
                ->visibleOn('create'),
            'tare_weight_kg' => TextInput::make('tare_weight_kg')
                ->numeric()
                ->minValue(0)
                ->step(0.001),
            'location_id' => Select::make('location_id')
                ->options(static fn (Get $get): array => self::locationOptions(SafeIntCastAction::cast($get('warehouse_id'), 0)))
                ->searchable()
                ->visibleOn('create'),
            'notes' => Textarea::make('notes')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function locationOptions(int $warehouseId): array
    {
        if ($warehouseId === 0) {
            return [];
        }

        return Location::query()
            ->with('area')
            ->where('is_active', true)
            ->whereIn('area_id', Area::query()->select('id')->where('warehouse_id', $warehouseId)->where('is_active', true))
            ->orderBy('code')
            ->get()
            ->mapWithKeys(static fn (Location $location): array => [$location->id => $location->code.' — '.$location->area->name])
            ->all();
    }
}
