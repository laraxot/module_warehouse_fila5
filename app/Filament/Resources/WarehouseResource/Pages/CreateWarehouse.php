<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\WarehouseResource\Pages;

use Modules\Warehouse\Filament\Resources\WarehouseResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord;

class CreateWarehouse extends XotBaseCreateRecord
{
    protected static string $resource = WarehouseResource::class;
}
