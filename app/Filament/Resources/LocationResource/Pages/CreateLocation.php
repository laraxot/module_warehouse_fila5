<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\LocationResource\Pages;

use Modules\Warehouse\Filament\Resources\LocationResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord;

class CreateLocation extends XotBaseCreateRecord
{
    protected static string $resource = LocationResource::class;
}
