<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;

class AreaPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;

    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('area.viewAny');
    }

    public function view(UserContract $user, Area $area): bool
    {
        return $user->hasPermissionTo('area.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('area.create');
    }

    public function update(UserContract $user, Area $area): bool
    {
        return $user->hasPermissionTo('area.update');
    }

    public function delete(UserContract $user, Area $area): bool
    {
        return $user->hasPermissionTo('area.delete');
    }

    public function restore(UserContract $user, Area $area): bool
    {
        return $user->hasPermissionTo('area.restore');
    }

    public function forceDelete(UserContract $user, Area $area): bool
    {
        return $user->hasPermissionTo('area.forceDelete');
    }
}
