<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea i permessi controllati dalle policy del modulo Warehouse (`{modello}.{abilità}`), guard `web`.
 *
 * Idempotente (findOrCreate). Senza questi permessi `hasPermissionTo()` lancia `PermissionDoesNotExist`
 * per ogni utente che non sia super-admin (audit sicurezza M4). I ruoli operativi che li ricevono sono
 * del prodotto (in Roosko: RooskoPermissionSeeder). Il test `PermissionSeederTest` verifica che l'elenco
 * copra ogni `hasPermissionTo()` delle policy.
 */
class WarehousePermissionSeeder extends Seeder
{
    private const string GUARD = 'web';

    /** @var array<string, list<string>> modello => abilità */
    public const array PERMISSIONS = [
        'area' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
        'location' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
        'stock_movement' => ['viewAny', 'view'],
        'stock_unit' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete', 'adjustWeight', 'move', 'void'],
        'warehouse' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::names() as $name) {
            Permission::findOrCreate($name, self::GUARD);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return list<string>
     */
    public static function names(): array
    {
        $names = [];
        foreach (self::PERMISSIONS as $model => $abilities) {
            foreach ($abilities as $ability) {
                $names[] = $model.'.'.$ability;
            }
        }

        return $names;
    }
}
