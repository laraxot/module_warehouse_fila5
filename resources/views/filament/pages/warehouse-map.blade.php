@php
    /*
     * Mappa del magazzino in SVG, generata lato server (nessun JS di terze parti).
     * Coordinate in metri: il viewBox è la planimetria, 1 unità SVG = 1 m.
     * I colori di occupazione sono i colori Filament del pannello (var(--{colore}-500)),
     * così mappa, legenda e widget usano la stessa scala definita in occupancy_level_enum.php.
     * Il testo che arriva dai dati (codici, nomi) passa sempre da {{ }} (escape).
     */
    /** @var \Modules\Warehouse\Filament\Pages\WarehouseMapPage $this */
    $map = $this->getMap();
    $selectedArea = $this->getSelectedArea();
    $units = $this->getSelectedAreaUnits();
    $options = $this->getWarehouseOptions();
    $fill = static fn (\Modules\Warehouse\Enums\OccupancyLevelEnum $level): string => 'var(--'.$level->getColor().'-500)';
@endphp

<x-filament-panels::page>
    <style>
        .wh-map-svg { width: 100%; height: auto; max-height: 75vh; background: rgba(128, 128, 128, .06); border-radius: .5rem; }
        .wh-map-area { cursor: pointer; fill-opacity: .45; stroke-width: .15; transition: fill-opacity .15s; }
        .wh-map-area:hover, .wh-map-area.is-selected { fill-opacity: .7; }
        .wh-map-area.is-selected { stroke-width: .35; }
        .wh-map-label { pointer-events: none; font-weight: 600; fill: currentColor; paint-order: stroke; stroke: rgba(255, 255, 255, .7); stroke-width: .08; }
        .wh-map-point { stroke: #fff; stroke-width: .06; }
        .wh-legend { display: flex; flex-wrap: wrap; gap: .75rem 1.25rem; font-size: .875rem; }
        .wh-legend-item { display: inline-flex; align-items: center; gap: .375rem; }
        .wh-legend-swatch { width: .875rem; height: .875rem; border-radius: .2rem; display: inline-block; }
        .wh-units { width: 100%; font-size: .875rem; border-collapse: collapse; }
        .wh-units th, .wh-units td { padding: .4rem .5rem; text-align: left; border-bottom: 1px solid rgba(128, 128, 128, .2); }
        .wh-units td.num, .wh-units th.num { text-align: right; font-variant-numeric: tabular-nums; }
    </style>

    <div style="max-width: 24rem;">
        <label for="wh-map-warehouse" style="display:block;font-size:.875rem;font-weight:500;margin-bottom:.25rem;">
            {{ __('warehouse::warehouse_map.fields.warehouse_id.label') }}
        </label>
        <x-filament::input.wrapper>
            <x-filament::input.select id="wh-map-warehouse" wire:model.live="warehouseId">
                @foreach ($options as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>

    @if ($map === null)
        <x-filament::section>
            {{ __('warehouse::warehouse_map.messages.no_warehouse') }}
        </x-filament::section>
    @else
        <x-filament::section>
            @if ($map->areas === [])
                <p style="margin-bottom:.75rem;">{{ __('warehouse::warehouse_map.messages.no_geometry') }}</p>
            @endif

            <svg
                class="wh-map-svg"
                viewBox="{{ $map->viewBox() }}"
                preserveAspectRatio="xMidYMid meet"
                role="img"
                aria-label="{{ __('warehouse::warehouse_map.svg.aria_label') }}"
            >
                @if ($map->backgroundUrl !== null)
                    <image href="{{ $map->backgroundUrl }}" x="0" y="0" width="{{ $map->widthM }}" height="{{ $map->heightM }}" preserveAspectRatio="none" opacity=".55" />
                @endif

                @foreach ($map->areas as $area)
                    @php
                        $level = $area->occupancy->level();
                        $percentage = $area->occupancy->percentage();
                        [$labelX, $labelY] = $area->labelPoint();
                        $isSelected = $selectedArea !== null && $selectedArea->id === $area->id;
                    @endphp
                    <g wire:key="wh-area-{{ $area->id }}">
                        <polygon
                            class="wh-map-area {{ $isSelected ? 'is-selected' : '' }}"
                            points="{{ $area->svgPoints() }}"
                            style="fill: {{ $fill($level) }}; stroke: {{ $area->strokeColor($fill($level)) }};"
                            wire:click="selectArea({{ $area->id }})"
                            tabindex="0"
                            role="button"
                            wire:keydown.enter="selectArea({{ $area->id }})"
                        >
                            <title>{{ __('warehouse::warehouse_map.svg.area_title', [
                                'code' => $area->code,
                                'name' => $area->name,
                                'load' => \Illuminate\Support\Number::format($area->occupancy->loadKg, 0),
                                'max' => $area->occupancy->maxKg === null ? '∞' : \Illuminate\Support\Number::format($area->occupancy->maxKg, 0),
                                'pct' => $percentage === null ? '—' : \Illuminate\Support\Number::format($percentage, 1),
                                'units' => $area->unitsCount,
                            ]) }}</title>
                        </polygon>
                        <text class="wh-map-label" x="{{ $labelX }}" y="{{ $labelY }}" text-anchor="middle" dominant-baseline="middle" font-size="{{ $map->fontSize() }}">
                            {{ $area->code }}@if ($percentage !== null) · {{ \Illuminate\Support\Number::format($percentage, 0) }}%@endif
                        </text>
                    </g>
                @endforeach

                @foreach ($map->locations as $location)
                    <circle
                        class="wh-map-point"
                        wire:key="wh-location-{{ $location->id }}"
                        cx="{{ $location->x }}"
                        cy="{{ $location->y }}"
                        r="{{ $map->pointRadius() }}"
                        style="fill: {{ $location->occupancy->loadKg > 0 ? $fill($location->occupancy->level()) : 'var(--gray-400)' }};"
                    >
                        <title>{{ __('warehouse::warehouse_map.svg.location_title', [
                            'code' => $location->code,
                            'load' => \Illuminate\Support\Number::format($location->occupancy->loadKg, 0),
                        ]) }}</title>
                    </circle>
                @endforeach
            </svg>

            <div class="wh-legend" style="margin-top:.75rem;">
                @foreach ($this->getLegend() as $level)
                    <span class="wh-legend-item">
                        <span class="wh-legend-swatch" style="background: {{ $fill($level) }};"></span>
                        {{ $level->getLabel() }}
                    </span>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                @if ($selectedArea === null)
                    {{ __('warehouse::warehouse_map.units.heading_empty') }}
                @else
                    {{ __('warehouse::warehouse_map.units.heading', ['code' => $selectedArea->code, 'name' => $selectedArea->name]) }}
                @endif
            </x-slot>

            @if ($selectedArea === null)
                <p>{{ __('warehouse::warehouse_map.messages.select_area') }}</p>
            @elseif ($units->isEmpty())
                <p>{{ __('warehouse::warehouse_map.messages.no_units') }}</p>
            @else
                <table class="wh-units">
                    <thead>
                        <tr>
                            <th>{{ __('warehouse::warehouse_map.units.columns.code') }}</th>
                            <th>{{ __('warehouse::warehouse_map.units.columns.location') }}</th>
                            <th>{{ __('warehouse::warehouse_map.units.columns.container_type') }}</th>
                            <th>{{ __('warehouse::warehouse_map.units.columns.status') }}</th>
                            <th class="num">{{ __('warehouse::warehouse_map.units.columns.net_weight_kg') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $unit)
                            <tr wire:key="wh-unit-{{ $unit->id }}">
                                <td>
                                    <x-filament::link :href="$this->getStockUnitUrl($unit)">{{ $unit->code }}</x-filament::link>
                                </td>
                                <td>{{ $unit->location?->code }}</td>
                                <td>{{ $unit->container_type->getLabel() }}</td>
                                <td>
                                    <x-filament::badge :color="$unit->status->getColor()">{{ $unit->status->getLabel() }}</x-filament::badge>
                                </td>
                                <td class="num">{{ \Illuminate\Support\Number::format($unit->netWeightKg(), 3) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">{{ __('warehouse::warehouse_map.units.total', ['count' => $units->count()]) }}</th>
                            <th class="num">{{ \Illuminate\Support\Number::format($units->sum(static fn ($unit): float => $unit->netWeightKg()), 3) }}</th>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>
