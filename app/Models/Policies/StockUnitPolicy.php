<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Warehouse\Models\StockUnit;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;
use Modules\Xot\Models\Policies\Concerns\RequiresOperationalPermissions;

class StockUnitPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;
    use RequiresOperationalPermissions;

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

    /** Stocca o sposta l'unità in una posizione (MoveStockUnitHeaderAction, pagina operatore). */
    public function move(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.move');
    }

    /** Rettifica del peso netto con motivo (AdjustStockUnitWeightHeaderAction). */
    public function adjustWeight(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.adjustWeight');
    }

    /** Annullamento dell'unità, irreversibile (VoidStockUnitHeaderAction). */
    public function void(UserContract $user, StockUnit $stockUnit): bool
    {
        return $user->hasPermissionTo('stock_unit.void');
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
