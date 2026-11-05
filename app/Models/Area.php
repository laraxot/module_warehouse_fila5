<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Warehouse\Enums\AreaTypeEnum;

/**
 * Area del magazzino (zona autorizzata con capienza e poligono sulla planimetria).
 *
 * @property int $id
 * @property int $warehouse_id
 * @property string $code
 * @property string $name
 * @property AreaTypeEnum $type
 * @property string|null $max_weight_kg
 * @property list<array{0: float, 1: float}>|null $map_geometry poligono [[x,y],...] in metri
 * @property string|null $color
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Warehouse $warehouse
 * @property-read Collection<int, Location> $locations
 * @property-read Collection<int, StockUnit> $stockUnits
 */
class Area extends BaseModel
{
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'warehouse_id',
        'code',
        'name',
        'type',
        'max_weight_kg',
        'map_geometry',
        'color',
        'is_active',
    ];

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return HasMany<Location, $this>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * @return HasManyThrough<StockUnit, Location, $this>
     */
    public function stockUnits(): HasManyThrough
    {
        return $this->hasManyThrough(StockUnit::class, Location::class);
    }

    public function maxWeightKg(): ?float
    {
        return $this->max_weight_kg === null ? null : (float) $this->max_weight_kg;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'warehouse_id' => 'integer',
            'type' => AreaTypeEnum::class,
            'max_weight_kg' => 'decimal:3',
            'map_geometry' => 'array',
            'is_active' => 'boolean',
        ]);
    }
}
