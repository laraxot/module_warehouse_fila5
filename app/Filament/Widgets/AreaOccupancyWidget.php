<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Livewire\Attributes\Reactive;
use Modules\Warehouse\Actions\GetAreaOccupancyAction;
use Modules\Warehouse\Models\Area;
use Modules\Xot\Filament\Widgets\XotBaseStatsOverviewWidget;

/**
 * Occupazione delle aree attive: carico / portata e percentuale, colore per fascia.
 * Sulla mappa riceve il magazzino selezionato (`warehouseId`); in dashboard mostra tutte le aree.
 */
class AreaOccupancyWidget extends XotBaseStatsOverviewWidget
{
    #[Reactive]
    public ?int $warehouseId = null;

    protected ?string $pollingInterval = '60s';

    protected function getHeading(): ?string
    {
        return static::trans('heading');
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $occupancy = app(GetAreaOccupancyAction::class);

        $query = Area::query()->with('warehouse')->where('is_active', true);
        if ($this->warehouseId !== null) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        return $query
            ->orderBy('warehouse_id')
            ->orderBy('code')
            ->get()
            ->map(static function (Area $area) use ($occupancy): Stat {
                $data = $occupancy->execute($area);
                $percentage = $data->percentage();
                $level = $data->level();

                $value = $percentage === null
                    ? static::trans('value_kg', false, ['load' => Number::format($data->loadKg, 0)])
                    : static::trans('value_pct', false, ['pct' => Number::format($percentage, 1)]);

                $description = $data->maxKg === null
                    ? static::trans('unbounded', false, ['load' => Number::format($data->loadKg, 0)])
                    : static::trans('load_of_max', false, ['load' => Number::format($data->loadKg, 0), 'max' => Number::format($data->maxKg, 0)]);

                return Stat::make($area->warehouse->code.' · '.$area->code.' — '.$area->name, $value)
                    ->description($description)
                    ->descriptionIcon($level->getIcon())
                    ->color($level->getColor());
            })
            ->values()
            ->all();
    }
}
