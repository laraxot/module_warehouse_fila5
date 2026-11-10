<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Models\Traits\HasIdentifiers;
use Modules\Warehouse\Exceptions\LocationCodeAlreadyUsedException;

/**
 * Posizione di stoccaggio (punto della planimetria, si etichetta e si scansiona).
 *
 * `code` è univoco nel magazzino (convenzione `{area}-{codice}`); il codice etichetta
 * aggiunge il codice del magazzino perché gli identificatori di Labeling sono univoci
 * su tutta l'installazione.
 *
 * @property int $id
 * @property int $area_id
 * @property string $code
 * @property string|null $name
 * @property string|null $x
 * @property string|null $y
 * @property string|null $max_weight_kg
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Area $area
 * @property-read Collection<int, StockUnit> $stockUnits
 */
class Location extends BaseModel implements LabelableContract
{
    use HasIdentifiers;
    use SoftDeletes;

    public const string LABEL_PREFIX = 'LOC';

    /** Colonna aggiunta da `withLoadKg()`. */
    public const string LOAD_KG_ATTRIBUTE = 'load_kg';

    /** Non `locations`: nome già usato dal modulo Geo sullo stesso database fisico. */
    protected $table = 'warehouse_locations';

    /** @var list<string> */
    protected $fillable = [
        'area_id',
        'code',
        'name',
        'x',
        'y',
        'max_weight_kg',
        'is_active',
    ];

    /**
     * @return BelongsTo<Area, $this>
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * @return HasMany<StockUnit, $this>
     */
    public function stockUnits(): HasMany
    {
        return $this->hasMany(StockUnit::class);
    }

    /**
     * Aggiunge alla query la colonna `load_kg` (somma delle unità che occupano la posizione) in un'unica
     * query aggregata invece di una query per riga. GetLocationLoadKgAction lo usa se presente (audit database R13).
     *
     * @param  Builder<Location>  $query
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
        $query->withSum([
            'stockUnits as '.self::LOAD_KG_ATTRIBUTE => static function (Builder $units): void {
                StockUnit::constrainToOccupying($units);
            },
        ], 'net_weight_kg');

        return $query;
    }

    /** Carico precalcolato da `withLoadKg()`, null se la query non lo ha caricato. */
    public function preloadedLoadKg(): ?float
    {
        $attributes = $this->getAttributes();
        if (! \array_key_exists(self::LOAD_KG_ATTRIBUTE, $attributes)) {
            return null;
        }

        return is_numeric($attributes[self::LOAD_KG_ATTRIBUTE]) ? round((float) $attributes[self::LOAD_KG_ATTRIBUTE], 3) : 0.0;
    }

    public function warehouseId(): int
    {
        return $this->area->warehouse_id;
    }

    public function maxWeightKg(): ?float
    {
        return $this->max_weight_kg === null ? null : (float) $this->max_weight_kg;
    }

    public function getLabelCode(): string
    {
        return self::LABEL_PREFIX.'-'.$this->area->warehouse->code.'-'.$this->code;
    }

    /**
     * @return array<string, string|int|float|null>
     */
    public function getLabelData(): array
    {
        $area = $this->area;

        return [
            'code' => $this->code,
            'label_code' => $this->getLabelCode(),
            'name' => $this->name,
            'area_code' => $area->code,
            'area_name' => $area->name,
            'area_type' => $area->type->value,
            'warehouse_code' => $area->warehouse->code,
            'warehouse_name' => $area->warehouse->name,
            'max_weight_kg' => $this->maxWeightKg(),
        ];
    }

    protected static function booted(): void
    {
        static::saving(static function (Location $location): void {
            $warehouseId = Area::query()->whereKey($location->area_id)->value('warehouse_id');

            $query = self::query()
                ->where('code', $location->code)
                ->whereIn('area_id', Area::query()->select('id')->where('warehouse_id', $warehouseId));

            if ($location->exists) {
                $query->whereKeyNot($location->getKey());
            }

            if ($query->exists()) {
                throw LocationCodeAlreadyUsedException::forCode($location->code);
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'area_id' => 'integer',
            'x' => 'decimal:2',
            'y' => 'decimal:2',
            'max_weight_kg' => 'decimal:3',
            'is_active' => 'boolean',
        ]);
    }
}
