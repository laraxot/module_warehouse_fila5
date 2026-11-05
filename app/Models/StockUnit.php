<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Models\Traits\HasIdentifiers;
use Modules\Warehouse\Enums\ContainerTypeEnum;
use Modules\Warehouse\Enums\StockUnitStatusEnum;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Unità di stoccaggio tracciata (big bag, fusto, cassone...).
 *
 * Peso, posizione e stato NON si modificano a mano: li aggiornano le Actions del modulo
 * (RecordStockMovementAction è l'unico scrittore dei movimenti, che sono la fonte di verità).
 * Il contenuto di dominio è il morph `subject` (in Roosko: WasteLot).
 *
 * @property int $id
 * @property int $warehouse_id
 * @property string $code
 * @property ContainerTypeEnum $container_type
 * @property StockUnitStatusEnum $status
 * @property int|null $location_id
 * @property string $net_weight_kg
 * @property string|null $tare_weight_kg
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property \Carbon\CarbonImmutable|null $received_at
 * @property string|null $notes
 * @property array<string, mixed>|null $extra_attributes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Warehouse $warehouse
 * @property-read Location|null $location
 * @property-read Model|null $subject
 * @property-read Collection<int, StockMovement> $movements
 *
 * @method static Builder<static> occupying()
 */
class StockUnit extends BaseModel implements LabelableContract
{
    use HasIdentifiers;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'warehouse_id',
        'code',
        'container_type',
        'status',
        'location_id',
        'net_weight_kg',
        'tare_weight_kg',
        'subject_type',
        'subject_id',
        'received_at',
        'notes',
        'extra_attributes',
    ];

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->orderBy('occurred_at')->orderBy('id');
    }

    public function netWeightKg(): float
    {
        return (float) $this->net_weight_kg;
    }

    public function tareWeightKg(): ?float
    {
        return $this->tare_weight_kg === null ? null : (float) $this->tare_weight_kg;
    }

    public function grossWeightKg(): float
    {
        return round($this->netWeightKg() + ($this->tareWeightKg() ?? 0.0), 3);
    }

    /**
     * Messaggio (tradotto) per una transizione di stato non consentita: usato dalle action
     * come messaggio di `Assert::true($unit->status->canTransitionTo($to), ...)`.
     */
    public function transitionDeniedMessage(StockUnitStatusEnum $to): string
    {
        return SafeStringCastAction::cast(__('warehouse::exceptions.invalid_state.transition', [
            'code' => $this->code,
            'from' => $this->status->getLabel(),
            'to' => $to->getLabel(),
        ]));
    }

    /**
     * Allinea questa istanza a una copia appena riletta (es. con lock) dalle action:
     * chi ha passato l'unità vede subito peso, posizione e stato aggiornati.
     */
    public function syncFrom(self $fresh): void
    {
        $this->setRawAttributes($fresh->getAttributes(), true);
        $this->setRelations([]);
    }

    /** L'unità occupa capacità nella sua posizione (conta nei carichi di aree e posizioni). */
    public function isOccupying(): bool
    {
        return $this->location_id !== null && $this->status->isOccupying();
    }

    public function getLabelCode(): string
    {
        return $this->code;
    }

    /**
     * @return array<string, string|int|float|null>
     */
    public function getLabelData(): array
    {
        $location = $this->location;

        return [
            'code' => $this->code,
            'container_type' => $this->container_type->value,
            'container_type_label' => $this->container_type->getLabel(),
            'status' => $this->status->value,
            'net_weight_kg' => $this->netWeightKg(),
            'tare_weight_kg' => $this->tareWeightKg(),
            'gross_weight_kg' => $this->grossWeightKg(),
            'warehouse_code' => $this->warehouse->code,
            'warehouse_name' => $this->warehouse->name,
            'location_code' => $location?->code,
            'area_code' => $location?->area->code,
            'received_at' => $this->received_at?->format('Y-m-d H:i'),
            'notes' => $this->notes,
        ];
    }

    /**
     * Unità che occupano capacità (pending/stored/reserved con una posizione).
     *
     * @param  Builder<StockUnit>  $query
     */
    public function scopeOccupying(Builder $query): void
    {
        self::constrainToOccupying($query);
    }

    /**
     * Stesso vincolo dello scope `occupying`, per i builder di relazione (es. `counts()` di Filament).
     *
     * @param  Builder<*>  $query
     */
    public static function constrainToOccupying(Builder $query): void
    {
        $query
            ->whereNotNull('location_id')
            ->whereIn('status', array_map(static fn (StockUnitStatusEnum $status): string => $status->value, StockUnitStatusEnum::occupying()));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'warehouse_id' => 'integer',
            'location_id' => 'integer',
            'subject_id' => 'integer',
            'container_type' => ContainerTypeEnum::class,
            'status' => StockUnitStatusEnum::class,
            'net_weight_kg' => 'decimal:3',
            'tare_weight_kg' => 'decimal:3',
            'received_at' => 'immutable_datetime',
            'extra_attributes' => 'array',
        ]);
    }
}
