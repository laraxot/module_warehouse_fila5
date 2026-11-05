<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Actions;

use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use InvalidArgumentException;
use Modules\Warehouse\Actions\VoidStockUnitAction;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Exceptions\WarehouseException;
use Modules\Warehouse\Filament\Actions\Concerns\TranslatesAction;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Actions\XotBaseAction;

/**
 * Annulla l'unità con motivo obbligatorio (VoidStockUnitAction). Operazione irreversibile.
 *
 * Etichetta/icona: `stock_unit.actions.void`; testi del modale: `void_stock_unit_header.php`.
 */
class VoidStockUnitHeaderAction extends XotBaseAction
{
    use TranslatesAction;

    public static function getDefaultName(): ?string
    {
        return 'void';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(static fn (StockUnit $record): string => static::transAction('modal_heading', ['code' => $record->code]))
            ->modalDescription(static fn (): string => static::transAction('modal_description'))
            ->schema(fn (): array => [
                'reason' => Textarea::make('reason')
                    ->required()
                    ->maxLength(1000)
                    ->rows(3),
            ])
            ->action(function (array $data, StockUnit $record): void {
                try {
                    app(VoidStockUnitAction::class)->execute($record, SafeStringCastAction::cast($data['reason'] ?? ''));
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
                    ->title(static::transAction('success', ['code' => $record->code]))
                    ->send();
            })
            ->visible(static fn (StockUnit $record): bool => $record->status->canTransitionTo(StockUnitStatusEnum::VOIDED));
    }
}
