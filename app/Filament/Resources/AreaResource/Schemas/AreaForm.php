<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\AreaResource\Schemas;

use Closure;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\Rules\Unique;
use Modules\Warehouse\Actions\ParseMapGeometryAction;
use Modules\Warehouse\Enums\AreaTypeEnum;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

class AreaForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [
            'warehouse_id' => Select::make('warehouse_id')
                ->relationship('warehouse', 'name')
                ->searchable()
                ->preload()
                ->required(),
            'code' => TextInput::make('code')
                ->required()
                ->maxLength(32)
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: static fn (Unique $rule, Get $get, mixed $livewire): Unique => $rule->where(
                        'warehouse_id',
                        SafeIntCastAction::cast($get('warehouse_id') ?? ($livewire instanceof RelationManager ? $livewire->getOwnerRecord()->getKey() : null)),
                    ),
                ),
            'name' => TextInput::make('name')
                ->required()
                ->maxLength(255),
            'type' => Select::make('type')
                ->options(AreaTypeEnum::class)
                ->required(),
            'max_weight_kg' => TextInput::make('max_weight_kg')
                ->numeric()
                ->minValue(0)
                ->step(0.001),
            'color' => ColorPicker::make('color')
                ->hex()
                ->regex(Area::COLOR_PATTERN),
            'map_geometry' => Textarea::make('map_geometry')
                ->rows(4)
                ->columnSpanFull()
                ->formatStateUsing(static fn (mixed $state): mixed => \is_array($state) ? json_encode($state, JSON_THROW_ON_ERROR) : $state)
                ->dehydrateStateUsing(static fn (mixed $state): ?array => app(ParseMapGeometryAction::class)->execute($state))
                ->rule(static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail): void {
                    if (! app(ParseMapGeometryAction::class)->isValid($value)) {
                        $fail(SafeStringCastAction::cast(__('warehouse::exceptions.geometry.invalid')));
                    }
                }),
            'is_active' => Toggle::make('is_active')
                ->default(true),
        ];
    }
}
