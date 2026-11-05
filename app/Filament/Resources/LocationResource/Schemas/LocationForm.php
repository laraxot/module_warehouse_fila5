<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\LocationResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * Il codice è univoco nel magazzino: lo garantisce il modello Location (LocationCodeAlreadyUsedException).
 */
class LocationForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'area_id' => Select::make('area_id')
                ->relationship('area', 'name')
                ->getOptionLabelFromRecordUsing(static fn (Area $record): string => $record->warehouse->code.' / '.$record->code.' — '.$record->name)
                ->searchable()
                ->preload()
                ->required(),
            'code' => TextInput::make('code')
                ->required()
                ->maxLength(64),
            'name' => TextInput::make('name')
                ->maxLength(255),
            'x' => TextInput::make('x')
                ->numeric()
                ->minValue(0)
                ->step(0.01),
            'y' => TextInput::make('y')
                ->numeric()
                ->minValue(0)
                ->step(0.01),
            'max_weight_kg' => TextInput::make('max_weight_kg')
                ->numeric()
                ->minValue(0)
                ->step(0.001),
            'is_active' => Toggle::make('is_active')
                ->default(true),
        ];
    }
}
