<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Modules\Warehouse\Enums\MovementTypeEnum;
use Modules\Warehouse\Exceptions\ImmutableStockMovementException;

/**
 * Movimento di magazzino: registro immutabile (solo insert).
 *
 * Scritto esclusivamente da RecordStockMovementAction. Update e delete lanciano
 * ImmutableStockMovementException: una correzione è un nuovo movimento (`adjustment`).
 *
 * @property int $id
 * @property int $stock_unit_id
 * @property MovementTypeEnum $type
 * @property string $quantity_kg
 * @property string $weight_before_kg
 * @property string $weight_after_kg
 * @property int|null $from_location_id
 * @property int|null $to_location_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property CarbonImmutable $occurred_at
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read StockUnit $stockUnit
 * @property-read Location|null $fromLocation
 * @property-read Location|null $toLocation
 * @property-read Model|null $reference
 */
class StockMovement extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'stock_unit_id',
        'type',
        'quantity_kg',
        'weight_before_kg',
        'weight_after_kg',
        'from_location_id',
        'to_location_id',
        'reference_type',
        'reference_id',
        'occurred_at',
        'notes',
    ];

    /**
     * @return BelongsTo<StockUnit, $this>
     */
    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class);
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id')->withTrashed();
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id')->withTrashed();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function quantityKg(): float
    {
        return (float) $this->quantity_kg;
    }

    protected static function booted(): void
    {
        static::updating(static function (StockMovement $movement): never {
            throw ImmutableStockMovementException::forUpdate($movement->id);
        });

        static::deleting(static function (StockMovement $movement): never {
            throw ImmutableStockMovementException::forDelete($movement->id);
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'stock_unit_id' => 'integer',
            'from_location_id' => 'integer',
            'to_location_id' => 'integer',
            'reference_id' => 'integer',
            'type' => MovementTypeEnum::class,
            'quantity_kg' => 'decimal:3',
            'weight_before_kg' => 'decimal:3',
            'weight_after_kg' => 'decimal:3',
            'occurred_at' => 'immutable_datetime',
        ]);
    }
}
