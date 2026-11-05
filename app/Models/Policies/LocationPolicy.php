<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Warehouse\Models\Location;
use Modules\Xot\Contracts\UserContract;

class LocationPolicy extends UserBasePolicy
{
    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('location.viewAny');
    }

    public function view(UserContract $user, Location $location): bool
    {
        return $user->hasPermissionTo('location.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('location.create');
    }

    public function update(UserContract $user, Location $location): bool
    {
        return $user->hasPermissionTo('location.update');
    }

    public function delete(UserContract $user, Location $location): bool
    {
        return $user->hasPermissionTo('location.delete');
    }

    public function restore(UserContract $user, Location $location): bool
    {
        return $user->hasPermissionTo('location.restore');
    }

    public function forceDelete(UserContract $user, Location $location): bool
    {
        return $user->hasPermissionTo('location.forceDelete');
    }
}
