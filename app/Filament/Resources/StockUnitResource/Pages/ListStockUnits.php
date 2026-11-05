<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\StockUnitResource\Pages;

use Modules\Warehouse\Filament\Resources\StockUnitResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListStockUnits extends XotBaseListRecords
{
    protected static string $resource = StockUnitResource::class;
}
