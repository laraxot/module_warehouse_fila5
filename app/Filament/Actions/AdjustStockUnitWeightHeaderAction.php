<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Actions;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;
use Modules\Warehouse\Actions\AdjustStockUnitWeightAction;
use Modules\Warehouse\Exceptions\WarehouseException;
use Modules\Warehouse\Filament\Actions\Concerns\TranslatesAction;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Actions\XotBaseAction;

/**
 * Rettifica il peso netto dell'unità con motivo obbligatorio (AdjustStockUnitWeightAction).
 *
 * Richiede l'abilità `adjustWeight` della StockUnitPolicy (permesso `stock_unit.adjustWeight`).
 *
 * Etichetta/icona: `stock_unit.actions.adjust_weight`; testi del modale: `adjust_stock_unit_weight_header.php`.
 */
class AdjustStockUnitWeightHeaderAction extends XotBaseAction
{
    use TranslatesAction;

    public static function getDefaultName(): ?string
    {
        return 'adjust_weight';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-scale')
            ->color('warning')
            ->authorize('adjustWeight')
            ->modalHeading(static fn (StockUnit $record): string => static::transAction('modal_heading', ['code' => $record->code]))
            ->modalDescription(static fn (StockUnit $record): string => static::transAction('modal_description', ['kg' => $record->netWeightKg()]))
            ->fillForm(static fn (StockUnit $record): array => ['new_net_weight_kg' => $record->netWeightKg()])
            ->schema(fn (): array => [
                'new_net_weight_kg' => TextInput::make('new_net_weight_kg')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.001)
                    ->required(),
                'reason' => Textarea::make('reason')
                    ->required()
                    ->maxLength(1000)
                    ->rows(3),
            ])
            ->action(function (array $data, StockUnit $record): void {
                Gate::authorize('adjustWeight', $record);

                try {
                    app(AdjustStockUnitWeightAction::class)->execute(
                        $record,
                        SafeFloatCastAction::cast($data['new_net_weight_kg'] ?? null),
                        SafeStringCastAction::cast($data['reason'] ?? ''),
                    );
                } catch (WarehouseException|InvalidArgumentException $e) {
                    Notification::make()
                        ->danger()
                        ->title(static::transAction('error'))
                        ->body($e->getMessage())
                        ->send();

                    $this->halt();
                }

                Notification::make()
                    ->success()
                    ->title(static::transAction('success', ['code' => $record->code, 'kg' => $record->netWeightKg()]))
                    ->send();
            })
            ->visible(static fn (StockUnit $record): bool => $record->status->isInStock());
    }
}
