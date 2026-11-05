<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Modules\Labeling\Filament\Actions\PrintLabelHeaderAction;
use Modules\Warehouse\Filament\Actions\AdjustStockUnitWeightHeaderAction;
use Modules\Warehouse\Filament\Actions\MoveStockUnitHeaderAction;
use Modules\Warehouse\Filament\Actions\VoidStockUnitHeaderAction;
use Modules\Warehouse\Filament\Resources\StockUnitResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;

class ViewStockUnit extends XotBaseViewRecord
{
    protected static string $resource = StockUnitResource::class;

    /**
     * @return array<string, Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            'print_label' => PrintLabelHeaderAction::make(),
            'move' => MoveStockUnitHeaderAction::make(),
            'adjust_weight' => AdjustStockUnitWeightHeaderAction::make(),
            'void' => VoidStockUnitHeaderAction::make(),
            'edit' => EditAction::make(),
        ];
    }
}
