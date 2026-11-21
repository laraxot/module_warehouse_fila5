<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\WarehouseResource\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class WarehouseForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'code' => TextInput::make('code')
                ->required()
                ->maxLength(32)
                ->unique(ignoreRecord: true),
            'name' => TextInput::make('name')
                ->required()
                ->maxLength(255),
            'description' => Textarea::make('description')
                ->rows(3)
                ->columnSpanFull(),
            'map_width_m' => TextInput::make('map_width_m')
                ->numeric()
                ->minValue(0)
                ->step(0.01),
            'map_height_m' => TextInput::make('map_height_m')
                ->numeric()
                ->minValue(0)
                ->step(0.01),
            'map' => SpatieMediaLibraryFileUpload::make('map')
                ->collection(Warehouse::MAP_COLLECTION)
                ->image()
                ->acceptedFileTypes(Warehouse::MAP_MIME_TYPES)
                ->columnSpanFull(),
            'is_active' => Toggle::make('is_active')
                ->default(true),
        ];
    }
}
