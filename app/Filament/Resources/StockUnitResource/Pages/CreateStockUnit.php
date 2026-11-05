<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\Pages;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Exceptions\WarehouseException;
use Modules\Warehouse\Filament\Resources\StockUnitResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord;

/**
 * Una unità nasce solo da CreateStockUnitAction (codice, movimento d'origine, guard):
 * la pagina gli passa i dati del form invece di salvare il modello direttamente.
 */
class CreateStockUnit extends XotBaseCreateRecord
{
    protected static string $resource = StockUnitResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(CreateStockUnitAction::class)->execute(StockUnitData::fromFormState($data));
        } catch (WarehouseException|InvalidArgumentException $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage())
                ->send();

            throw new Halt;
        }
    }
}
