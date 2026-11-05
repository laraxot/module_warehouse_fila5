<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Contracts\UserContract;

class StockUnitPolicy extends UserBasePolicy
{
    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('stock_unit.viewAny');
    }

    public function view(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('stock_unit.create');
    }

    public function update(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.update');
    }

    public function delete(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.delete');
    }

    public function restore(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.restore');
    }

    public function forceDelete(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.forceDelete');
    }
}
