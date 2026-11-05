<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Datas\OccupancyData;
use Modules\Warehouse\Models\Area;
use Spatie\QueueableAction\QueueableAction;

/**
 * Occupazione di un'area: carico (GetAreaLoadKgAction) rispetto a `max_weight_kg`.
 */
class GetAreaOccupancyAction
{
    use QueueableAction;

    public function __construct(
        private readonly GetAreaLoadKgAction $getAreaLoad,
    ) {}

    public function execute(Area $area): OccupancyData
    {
        return new OccupancyData(
            loadKg: $this->getAreaLoad->execute($area),
            maxKg: $area->maxWeightKg(),
        );
    }
}
