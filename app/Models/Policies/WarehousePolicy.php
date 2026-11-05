<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Contracts\UserContract;

class WarehousePolicy extends UserBasePolicy
{
    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('warehouse.viewAny');
    }

    public function view(UserContract $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('warehouse.create');
    }

    public function update(UserContract $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.update');
    }

    public function delete(UserContract $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.delete');
    }

    public function restore(UserContract $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.restore');
    }

    public function forceDelete(UserContract $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('warehouse.forceDelete');
    }
}
