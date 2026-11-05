<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Warehouse\Models\StockMovement;
use Modules\Warehouse\Models\StockUnit;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Annullamento di una unità (registrata per errore, distrutta, smarrita) con motivo obbligatorio.
 * La storia dei movimenti resta: il movimento `void` porta la giacenza a 0 e libera la posizione.
 */
class VoidStockUnitAction
{
    use QueueableAction;

    public function __construct(
        private readonly RecordStockMovementAction $recordMovement,
        private readonly LockStockUnitAction $lockStockUnit,
    ) {}

    public function execute(StockUnit $unit, string $reason): StockMovement
    {
        $reason = trim($reason);
        Assert::stringNotEmpty($reason, SafeStringCastAction::cast(__('warehouse::exceptions.reason_required')));

        return $unit->getConnection()->transaction(function () use ($unit, $reason): StockMovement {
            $locked = $this->lockStockUnit->execute($unit);

            Assert::true(
                $locked->status->canTransitionTo(StockUnitStatusEnum::VOIDED),
                $locked->transitionDeniedMessage(StockUnitStatusEnum::VOIDED),
            );

            $movement = $this->recordMovement->execute($locked, MovementTypeEnum::VOID, $locked->netWeightKg(), $locked->location, null, null, $reason);

            $locked->status = StockUnitStatusEnum::VOIDED;
            $locked->save();

            $unit->syncFrom($locked);

            return $movement;
        });
    }
}
