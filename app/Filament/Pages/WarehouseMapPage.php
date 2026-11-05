<?php

declare(strict_types=1);

namespace Modules\Warehouse\Filament\Pages;

use Illuminate\Database\Eloquent\Collection;
use Modules\Warehouse\Actions\BuildWarehouseMapAction;
use Modules\Warehouse\Actions\ListAreaStockUnitsAction;
use Modules\Warehouse\Datas\WarehouseMapData;
use Modules\Warehouse\Enums\OccupancyLevelEnum;
use Modules\Warehouse\Filament\Resources\StockUnitResource;
use Modules\Warehouse\Filament\Widgets\AreaOccupancyWidget;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Modules\Xot\Filament\Pages\XotBasePage;

/**
 * Mappa del magazzino: SVG generato lato server (BuildWarehouseMapAction) con le aree colorate
 * per occupazione, i punti posizione e, se caricata, la planimetria come sfondo.
 * Il click su un'area mostra le unità presenti (ListAreaStockUnitsAction).
 *
 * La pagina tiene solo lo stato della scelta dell'utente; i dati vengono dalle Actions.
 */
class WarehouseMapPage extends XotBasePage
{
    public ?int $warehouseId = null;

    public ?int $selectedAreaId = null;

    protected string $view = 'warehouse::filament.pages.warehouse-map';

    public static function getNavigationLabel(): string
    {
        return static::transFunc(__FUNCTION__);
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-map';
    }

    public function getTitle(): string
    {
        return static::transFunc(__FUNCTION__);
    }

    public function mount(): void
    {
        if ($this->warehouseId !== null) {
            return;
        }

        $first = Warehouse::query()->where('is_active', true)->orderBy('name')->first();
        $this->warehouseId = $first?->id;
    }

    public function updatedWarehouseId(): void
    {
        $this->selectedAreaId = null;
    }

    public function selectArea(int $areaId): void
    {
        $belongs = Area::query()
            ->whereKey($areaId)
            ->where('warehouse_id', $this->warehouseId)
            ->exists();

        $this->selectedAreaId = $belongs && $this->selectedAreaId !== $areaId ? $areaId : null;
    }

    /**
     * @return array<int, string>
     */
    public function getWarehouseOptions(): array
    {
        return Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(static fn (Warehouse $warehouse): array => [$warehouse->id => $warehouse->name])
            ->all();
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouseId === null ? null : Warehouse::query()->find($this->warehouseId);
    }

    public function getMap(): ?WarehouseMapData
    {
        $warehouse = $this->getWarehouse();

        return $warehouse === null ? null : app(BuildWarehouseMapAction::class)->execute($warehouse);
    }

    public function getSelectedArea(): ?Area
    {
        if ($this->selectedAreaId === null) {
            return null;
        }

        return Area::query()->where('warehouse_id', $this->warehouseId)->find($this->selectedAreaId);
    }

    /**
     * @return Collection<int, StockUnit>
     */
    public function getSelectedAreaUnits(): Collection
    {
        $area = $this->getSelectedArea();

        return $area === null ? new Collection : app(ListAreaStockUnitsAction::class)->execute($area);
    }

    public function getStockUnitUrl(StockUnit $unit): string
    {
        return StockUnitResource::getUrl('view', ['record' => $unit]);
    }

    /**
     * Fasce della legenda, nell'ordine di visualizzazione.
     *
     * @return list<OccupancyLevelEnum>
     */
    public function getLegend(): array
    {
        return OccupancyLevelEnum::cases();
    }

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            AreaOccupancyWidget::class,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getWidgetData(): array
    {
        return [
            'warehouseId' => $this->warehouseId,
        ];
    }
}
