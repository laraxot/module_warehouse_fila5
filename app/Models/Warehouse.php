<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Magazzino fisico con planimetria opzionale (media collection `map`).
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string|null $map_width_m
 * @property string|null $map_height_m
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Area> $areas
 * @property-read Collection<int, Location> $locations
 * @property-read Collection<int, StockUnit> $stockUnits
 */
class Warehouse extends BaseModel implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    public const string MAP_COLLECTION = 'map';

    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'description',
        'map_width_m',
        'map_height_m',
        'is_active',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MAP_COLLECTION)
            ->singleFile()
            ->acceptsMimeTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml']);
    }

    /**
     * @return HasMany<Area, $this>
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * @return HasManyThrough<Location, Area, $this>
     */
    public function locations(): HasManyThrough
    {
        return $this->hasManyThrough(Location::class, Area::class);
    }

    /**
     * @return HasMany<StockUnit, $this>
     */
    public function stockUnits(): HasMany
    {
        return $this->hasMany(StockUnit::class);
    }

    /** URL della planimetria, se caricata. */
    public function getMapUrl(): ?string
    {
        $url = $this->getFirstMediaUrl(self::MAP_COLLECTION);

        return $url === '' ? null : $url;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'map_width_m' => 'decimal:2',
            'map_height_m' => 'decimal:2',
            'is_active' => 'boolean',
        ]);
    }
}
