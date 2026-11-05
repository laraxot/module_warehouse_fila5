<?php

declare(strict_types=1);

namespace Modules\Warehouse\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Modules\User\Models\User;
use Modules\User\Providers\UserServiceProvider;
use Modules\Warehouse\Providers\WarehouseServiceProvider;
use Modules\Xot\Tests\XotBaseTestCase;

/**
 * Base test case del modulo Warehouse.
 *
 * Usa il sqlite condiviso (nessun migrate:fresh). Se la connessione `warehouse` non è
 * configurata la mappa sul sqlite condiviso; se le tabelle del modulo mancano esegue SOLO
 * le migrazioni del modulo (additive), prima che inizino le transazioni dei test.
 */
abstract class TestCase extends XotBaseTestCase
{
    use DatabaseTransactions;

    /** @var list<string> */
    protected $connectionsToTransact = ['sqlite', 'warehouse', 'user'];

    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->ensureWarehouseConnection();
        $this->prepareSharedSqliteForTesting();
        $this->ensureWarehouseSchema();

        parent::setUp();

        config(['auth.providers.users.model' => User::class]);
    }

    /**
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders(Application $app): array
    {
        return [
            ...parent::getPackageProviders($app),
            UserServiceProvider::class,
            WarehouseServiceProvider::class,
        ];
    }

    private function ensureWarehouseConnection(): void
    {
        if (\is_array(config('database.connections.warehouse'))) {
            return;
        }

        config(['database.connections.warehouse' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]]);
    }

    private function ensureWarehouseSchema(): void
    {
        if (Schema::connection('warehouse')->hasTable('stock_movements')) {
            return;
        }

        Artisan::call('migrate', [
            '--path' => module_path('Warehouse', 'database/migrations'),
            '--realpath' => true,
            '--force' => true,
        ]);
    }
}
