<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Modules\Warehouse\Filament\Resources\StockUnitResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord;

/**
 * Si modificano solo i dati descrittivi: peso, posizione e stato cambiano con le azioni
 * della pagina di dettaglio. Niente eliminazione: un'unità registrata per errore si annulla.
 */
class EditStockUnit extends XotBaseEditRecord
{
    protected static string $resource = StockUnitResource::class;

    /**
     * @return array<string, Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            'view' => ViewAction::make(),
        ];
    }
}
