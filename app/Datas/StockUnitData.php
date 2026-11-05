<?php

declare(strict_types=1);

namespace Modules\Warehouse\Datas;

use Illuminate\Database\Eloquent\Model;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Xot\Actions\Cast\SafeFloatCastAction;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Dati per creare una unità di stoccaggio (CreateStockUnitAction).
 *
 * `subject` è il contenuto di dominio (morph): Optional quando chi chiama non lo passa,
 * null quando lo azzera esplicitamente. Per CreateStockUnitAction i due casi coincidono.
 */
final class StockUnitData extends Data
{
    public function __construct(
        public readonly int $warehouseId,
        public readonly ContainerTypeEnum $containerType,
        public readonly float $netWeightKg,
        public readonly ?float $tareWeightKg = null,
        public readonly ?int $locationId = null,
        public readonly Model|Optional|null $subject = null,
        public readonly ?string $notes = null,
    ) {}

    /**
     * Dallo stato del form Filament (chiavi snake_case dei campi della StockUnitForm).
     *
     * @param  array<string, mixed>  $state
     */
    public static function fromFormState(array $state): self
    {
        $container = $state['container_type'] ?? null;
        $tare = $state['tare_weight_kg'] ?? null;
        $location = $state['location_id'] ?? null;
        $notes = $state['notes'] ?? null;

        return new self(
            warehouseId: SafeIntCastAction::cast($state['warehouse_id'] ?? null),
            containerType: $container instanceof ContainerTypeEnum ? $container : ContainerTypeEnum::from(\is_string($container) ? $container : ''),
            netWeightKg: SafeFloatCastAction::cast($state['net_weight_kg'] ?? null),
            tareWeightKg: $tare === null || $tare === '' ? null : SafeFloatCastAction::cast($tare),
            locationId: $location === null || $location === '' ? null : SafeIntCastAction::cast($location),
            notes: \is_string($notes) && trim($notes) !== '' ? trim($notes) : null,
        );
    }

    public function subjectModel(): ?Model
    {
        return $this->subject instanceof Model ? $this->subject : null;
    }
}
