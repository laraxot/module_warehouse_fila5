<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Warehouse\Enums\AreaTypeEnum;
use Modules\Warehouse\Exceptions\InvalidAreaColorException;

use function Safe\preg_match;

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

    /** Unico formato ammesso per `color` (finisce nell'attributo `style` della mappa). */
    public const string COLOR_PATTERN = '/^#[0-9a-fA-F]{6}$/';

    /** Colonna aggiunta da `withLoadKg()`. */
    public const string LOAD_KG_ATTRIBUTE = 'load_kg';

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

    /**
     * Aggiunge alla query la colonna `load_kg`: carico dell'area (unità che occupano capacità nelle sue posizioni,
     * anche posizioni eliminate), calcolato con una sottoquery correlata invece di una query per riga.
     * GetAreaLoadKgAction lo usa se presente (tabelle, widget, mappa: audit database R13).
     *
     * @param  Builder<Area>  $query
     */
    public function scopeWithLoadKg(Builder $query): void
    {
        self::applyLoadKg($query);
    }

    /**
     * Come lo scope `withLoadKg()`, per i builder non tipizzati (es. `modifyQueryUsing` delle tabelle Filament).
     *
     * @param  Builder<*>  $query
     * @return Builder<*>
     */
    public static function applyLoadKg(Builder $query): Builder
    {
        if ($query->getQuery()->columns === null) {
            $query->select((new self)->qualifyColumn('*'));
        }

        $locations = Location::withTrashed()
            ->select('id')
            ->whereColumn((new Location)->qualifyColumn('area_id'), (new self)->qualifyColumn('id'));

        $query->selectSub(
            StockUnit::query()->occupying()->whereIn('location_id', $locations)->selectRaw('coalesce(sum(net_weight_kg), 0)'),
            self::LOAD_KG_ATTRIBUTE,
        );

        return $query;
    }

    /** Carico precalcolato da `withLoadKg()`, null se la query non lo ha caricato. */
    public function preloadedLoadKg(): ?float
    {
        $attributes = $this->getAttributes();

        return \array_key_exists(self::LOAD_KG_ATTRIBUTE, $attributes) && is_numeric($attributes[self::LOAD_KG_ATTRIBUTE])
            ? round((float) $attributes[self::LOAD_KG_ATTRIBUTE], 3)
            : null;
    }

    /** Il valore è un colore `#rrggbb` (null non è un colore). */
    public static function isValidColor(mixed $value): bool
    {
        return \is_string($value) && preg_match(self::COLOR_PATTERN, $value) === 1;
    }

    /** Colore sicuro da scrivere in CSS: il valore salvato se valido, altrimenti null. */
    public function safeColor(): ?string
    {
        return self::isValidColor($this->color) ? $this->color : null;
    }

    protected static function booted(): void
    {
        static::saving(static function (Area $area): void {
            if ($area->color === '') {
                $area->color = null;
            }

            if ($area->color !== null && ! self::isValidColor($area->color)) {
                throw InvalidAreaColorException::forValue($area->color);
            }
        });
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
