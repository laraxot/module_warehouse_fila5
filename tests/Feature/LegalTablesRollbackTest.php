<?php

declare(strict_types=1);

use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * Le tabelle con dati legali o di tracciabilità non si eliminano con un rollback (audit database R15).
 */
test('rollback of a legal data table is refused and the table stays', function (string $file): void {
    $migration = require dirname(__DIR__, 2).'/database/migrations/'.$file;
    if (! \is_object($migration) || ! method_exists($migration, 'down') || ! method_exists($migration, 'getTable') || ! method_exists($migration, 'getConn')) {
        throw new LogicException('Migrazione inattesa: '.$file);
    }

    try {
        $migration->down();
        Assert::fail('down() deve rifiutare il rollback');
    } catch (LogicException $e) {
        Assert::assertStringContainsString('Rollback', $e->getMessage());
    }

    $schema = $migration->getConn();
    $table = $migration->getTable();
    if (! $schema instanceof Illuminate\Database\Schema\Builder || ! \is_string($table)) {
        throw new LogicException('Migrazione inattesa: '.$file);
    }

    Assert::assertTrue($schema->hasTable($table));
})->with([
    '2026_11_04_100300_create_stock_units_table.php',
    '2026_11_04_100400_create_stock_movements_table.php',
]);
