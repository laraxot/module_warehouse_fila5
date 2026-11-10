<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Warehouse\Models\StockMovement;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;

/**
 * I movimenti sono un registro immutabile: si leggono e basta, anche per il super-admin.
 * Si creano solo tramite le Actions di dominio (RecordStockMovementAction), mai dalla UI.
 */
class StockMovementPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;

    /** @var list<string> */
    private const array WRITE_ABILITIES = ['create', 'update', 'delete', 'deleteAny', 'restore', 'forceDelete', 'replicate', 'reorder'];

    public function before(UserContract $user, string $_ability): ?bool
    {
        if (\in_array($_ability, self::WRITE_ABILITIES, true)) {
            return false;
        }

        return parent::before($user, $_ability);
    }

    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('stock_movement.viewAny');
    }

    public function view(UserContract $user, StockMovement $stockMovement): bool
    {
        return $user->hasPermissionTo('stock_movement.view');
    }

    public function create(UserContract $user): bool
    {
        return false;
    }

    public function update(UserContract $user, StockMovement $stockMovement): bool
    {
        return false;
    }

    public function delete(UserContract $user, StockMovement $stockMovement): bool
    {
        return false;
    }
}
