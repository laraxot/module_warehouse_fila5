<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\AreaResource\Pages;

use Modules\Warehouse\Filament\Resources\AreaResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListAreas extends XotBaseListRecords
{
    protected static string $resource = AreaResource::class;
}
