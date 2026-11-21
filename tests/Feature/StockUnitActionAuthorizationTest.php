<?php

declare(strict_types=1);

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Warehouse\Actions\CreateStockUnitAction;
use Modules\Warehouse\Datas\StockUnitData;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Filament\Actions\AdjustStockUnitWeightHeaderAction;
use Modules\Warehouse\Filament\Actions\MoveStockUnitHeaderAction;
use Modules\Warehouse\Filament\Actions\VoidStockUnitHeaderAction;
use Modules\Warehouse\Filament\Pages\WarehouseMapPage;
use Modules\Warehouse\Filament\Widgets\AreaOccupancyWidget;
use Modules\Warehouse\Models\Policies\AreaPolicy;
use Modules\Warehouse\Models\Policies\LocationPolicy;
use Modules\Warehouse\Models\Policies\StockMovementPolicy;
use Modules\Warehouse\Models\Policies\StockUnitPolicy;
use Modules\Warehouse\Models\Policies\WarehousePolicy;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use Modules\Xot\Contracts\UserContract;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * Utente fittizio: le abilità consentite le decide un Gate::before, senza tabelle di permessi.
 *
 * @param  list<string>  $abilities
 */
function warehouseActingWith(array $abilities): void
{
    Auth::setUser(new class(['id' => 1]) extends GenericUser implements AuthorizableContract
    {
        use Authorizable;
    });
    Gate::before(static fn (mixed $_user, string $ability): bool => in_array($ability, $abilities, true));
}

function authorizationUnit(): StockUnit
{
    return app(CreateStockUnitAction::class)->execute(new StockUnitData(
        warehouseId: Warehouse::factory()->createOne()->id,
        containerType: ContainerTypeEnum::BOX,
        netWeightKg: 10.0,
    ));
}

test('move, adjust weight and void need their own ability, not just the status', function (): void {
    warehouseActingWith(['view', 'update']);
    $unit = authorizationUnit();

    Assert::assertFalse(MoveStockUnitHeaderAction::make()->record($unit)->isAuthorized());
    Assert::assertFalse(AdjustStockUnitWeightHeaderAction::make()->record($unit)->isAuthorized());
    Assert::assertFalse(VoidStockUnitHeaderAction::make()->record($unit)->isAuthorized());
});

test('each stock unit action is authorized by its ability', function (): void {
    warehouseActingWith(['move', 'adjustWeight', 'void']);
    $unit = authorizationUnit();

    Assert::assertTrue(MoveStockUnitHeaderAction::make()->record($unit)->isAuthorized());
    Assert::assertTrue(AdjustStockUnitWeightHeaderAction::make()->record($unit)->isAuthorized());
    Assert::assertTrue(VoidStockUnitHeaderAction::make()->record($unit)->isAuthorized());
});

test('map page and occupancy widget are closed without viewAny', function (): void {
    warehouseActingWith([]);

    Assert::assertFalse(WarehouseMapPage::canAccess());
    Assert::assertFalse(AreaOccupancyWidget::canView());
});

test('map page and occupancy widget open with viewAny', function (): void {
    warehouseActingWith(['viewAny']);

    Assert::assertTrue(WarehouseMapPage::canAccess());
    Assert::assertTrue(AreaOccupancyWidget::canView());
});

test('every policy denies the Filament abilities it does not declare', function (string $policy): void {
    /** @var UserContract&Mockery\MockInterface $user */
    $user = Mockery::mock(UserContract::class);
    $instance = new $policy;
    if (! method_exists($instance, 'deleteAny') || ! method_exists($instance, 'forceDeleteAny') || ! method_exists($instance, 'restoreAny') || ! method_exists($instance, 'reorder')) {
        throw new LogicException($policy.' non usa DeniesUndeclaredFilamentAbilities');
    }

    Assert::assertFalse($instance->deleteAny($user));
    Assert::assertFalse($instance->restoreAny($user));
    Assert::assertFalse($instance->reorder($user));
})->with([
    AreaPolicy::class,
    LocationPolicy::class,
    StockMovementPolicy::class,
    StockUnitPolicy::class,
    WarehousePolicy::class,
]);

test('the stock unit abilities check dedicated permissions', function (): void {
    /** @var UserContract&Mockery\MockInterface $user */
    $user = Mockery::mock(UserContract::class);
    $user->shouldReceive('hasPermissionTo')->with('stock_unit.move')->andReturn(true);
    $user->shouldReceive('hasPermissionTo')->with('stock_unit.adjustWeight')->andReturn(false);
    $user->shouldReceive('hasPermissionTo')->with('stock_unit.void')->andReturn(false);
    $unit = new StockUnit;
    $policy = new StockUnitPolicy;

    Assert::assertTrue($policy->move($user, $unit));
    Assert::assertFalse($policy->adjustWeight($user, $unit));
    Assert::assertFalse($policy->void($user, $unit));
});
