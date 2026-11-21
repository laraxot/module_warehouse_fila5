<?php

declare(strict_types=1);

use Modules\Warehouse\Database\Seeders\WarehousePermissionSeeder;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Ogni permesso controllato da una policy del modulo deve essere creato dal seeder (audit sicurezza M4):
 * altrimenti spatie lancia PermissionDoesNotExist e la pagina va in errore 500.
 */
test('the permission seeder covers every permission checked by the policies', function (): void {
    $checked = [];
    foreach (glob(dirname(__DIR__, 2).'/app/Models/Policies/*.php') ?: [] as $file) {
        preg_match_all("/hasPermissionTo\\('([^']+)'\\)/", (string) file_get_contents($file), $matches);
        $checked = [...$checked, ...$matches[1]];
    }

    Assert::assertNotEmpty($checked);
    Assert::assertSame([], array_values(array_diff(array_unique($checked), WarehousePermissionSeeder::names())));
});

test('the module database seeder calls the permission seeder', function (): void {
    $source = (string) file_get_contents(dirname(__DIR__, 2).'/database/seeders/WarehouseDatabaseSeeder.php');

    Assert::assertStringContainsString('WarehousePermissionSeeder::class', $source);
});

test('the seeder creates the permissions and can run twice', function (): void {
    $seeder = new WarehousePermissionSeeder;
    $seeder->run();
    $seeder->run();

    $existing = \Modules\User\Models\Permission::query()
        ->where('guard_name', 'web')
        ->whereIn('name', WarehousePermissionSeeder::names())
        ->pluck('name')
        ->all();

    Assert::assertEqualsCanonicalizing(WarehousePermissionSeeder::names(), $existing);
});
