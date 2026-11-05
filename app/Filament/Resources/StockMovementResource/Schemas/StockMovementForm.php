<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockMovementResource\Schemas;

use Filament\Schemas\Components\Component;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

/**
 * Registro immutabile: nessun campo modificabile. La classe esiste perché XotBaseResource
 * risolve sempre un `{Model}Form`.
 */
class StockMovementForm extends XotBaseResourceForm
{
    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        return [];
    }
}
