<?php

declare(strict_types=1);

namespace Modules\Warehouse\Providers;

use Modules\Xot\Providers\XotBaseServiceProvider;
use Override;

/**
 * Service Provider del modulo Warehouse.
 *
 * La config viene unita già in register(): così i moduli che estendono
 * `warehouse.placement_guards` con config()->push() nel proprio boot()
 * trovano sempre il default del modulo e non lo sovrascrivono.
 */
class WarehouseServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Warehouse';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    #[Override]
    public function register(): void
    {
        parent::register();

        $this->registerConfig();
    }

    #[Override]
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path('warehouse.php'),
        ], 'config');

        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), 'warehouse');
    }
}
