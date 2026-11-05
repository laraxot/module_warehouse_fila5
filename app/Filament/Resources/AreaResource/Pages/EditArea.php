<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Resources\AreaResource\Pages;

use Modules\Warehouse\Filament\Resources\AreaResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord;

class EditArea extends XotBaseEditRecord
{
    protected static string $resource = AreaResource::class;
}
