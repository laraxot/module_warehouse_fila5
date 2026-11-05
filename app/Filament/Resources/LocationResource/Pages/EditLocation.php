<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\LocationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Modules\Labeling\Filament\Actions\PrintLabelHeaderAction;
use Modules\Warehouse\Filament\Resources\LocationResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord;

class EditLocation extends XotBaseEditRecord
{
    protected static string $resource = LocationResource::class;

    /**
     * @return array<string, Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            'print_label' => PrintLabelHeaderAction::make(),
            ...parent::getHeaderActions(),
        ];
    }
}
