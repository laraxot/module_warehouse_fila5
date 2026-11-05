<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Actions;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use InvalidArgumentException;
use Modules\Warehouse\Actions\PutAwayStockUnitAction;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Exceptions\WarehouseException;
use Modules\Warehouse\Filament\Actions\Concerns\TranslatesAction;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Filament\Actions\XotBaseAction;

/**
 * Stocca o sposta l'unità in una posizione del suo magazzino (PutAwayStockUnitAction).
 * Un rifiuto dei guard di posizionamento resta nel modale con il motivo.
 *
 * Etichetta/icona: `stock_unit.actions.move`; testi del modale: `move_stock_unit_header.php`.
 */
class MoveStockUnitHeaderAction extends XotBaseAction
{
    use TranslatesAction;

    public static function getDefaultName(): ?string
    {
        return 'move';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-arrows-right-left')
            ->color('primary')
            ->modalHeading(static fn (StockUnit $record): string => static::transAction('modal_heading', ['code' => $record->code]))
            ->modalDescription(static fn (): string => static::transAction('modal_description'))
            ->schema(fn (StockUnit $record): array => [
                'location_id' => Select::make('location_id')
                    ->options(static fn (): array => self::locationOptions($record))
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data, StockUnit $record): void {
                $location = Location::query()->findOrFail(SafeIntCastAction::cast($data['location_id'] ?? null));

                try {
                    app(PutAwayStockUnitAction::class)->execute($record, $location);
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
                    ->title(static::transAction('success', ['code' => $record->code, 'location' => $location->code]))
                    ->send();
            })
            ->visible(static fn (StockUnit $record): bool => $record->status->canTransitionTo(StockUnitStatusEnum::STORED));
    }

    /**
     * Posizioni attive del magazzino dell'unità, esclusa quella attuale: id => "codice — area".
     *
     * @return array<int, string>
     */
    private static function locationOptions(StockUnit $unit): array
    {
        $query = Location::query()
            ->with('area')
            ->where('is_active', true)
            ->whereIn('area_id', Area::query()->select('id')->where('warehouse_id', $unit->warehouse_id)->where('is_active', true));

        if ($unit->location_id !== null) {
            $query->whereKeyNot($unit->location_id);
        }

        return $query
            ->orderBy('code')
            ->get()
            ->mapWithKeys(static fn (Location $location): array => [$location->id => $location->code.' — '.$location->area->name])
            ->all();
    }
}
