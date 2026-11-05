<?php

declare(strict_types=1);

namespace Modules\Warehouse\Providers;

use Modules\Xot\Providers\XotBaseRouteServiceProvider;

class RouteServiceProvider extends XotBaseRouteServiceProvider
{
    public string $name = 'Warehouse';

    protected string $moduleNamespace = 'Modules\Warehouse\Http\Controllers';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;
}
