<?php

declare(strict_types=1);

namespace Modules\Warehouse\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Warehouse\Enums\AreaTypeEnum;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Location;
use Modules\Warehouse\Models\Warehouse;

/**
 * Magazzino demo `DEMO` (80 × 50 m) con le cinque aree tipiche del flusso e una griglia di posizioni.
 * Idempotente: si può rilanciare senza duplicare.
 */
class WarehouseSeeder extends Seeder
{
    public const string DEMO_CODE = 'DEMO';

    /**
     * code => [tipo, nome, portata kg, poligono, colore, colonne × righe posizioni, portata posizione kg]
     *
     * @var array<string, array{0: AreaTypeEnum, 1: string, 2: float|null, 3: non-empty-list<array{0: int, 1: int}>, 4: string, 5: array{0: int, 1: int}, 6: float|null}>
     */
    private const array AREAS = [
        'IN' => [AreaTypeEnum::INBOUND, 'Ricevimento', 40000.0, [[2, 2], [22, 2], [22, 18], [2, 18]], '#0ea5e9', [3, 2], 8000.0],
        'QU' => [AreaTypeEnum::QUARANTINE, 'Quarantena', 15000.0, [[2, 22], [22, 22], [22, 34], [2, 34]], '#f59e0b', [3, 1], 5000.0],
        'ST' => [AreaTypeEnum::STORAGE, 'Stoccaggio', 200000.0, [[26, 2], [62, 2], [62, 34], [26, 34]], '#22c55e', [6, 4], 10000.0],
        'PR' => [AreaTypeEnum::PROCESSING, 'Lavorazione', 30000.0, [[2, 38], [40, 38], [40, 48], [2, 48]], '#8b5cf6', [4, 1], 8000.0],
        'OUT' => [AreaTypeEnum::OUTBOUND, 'Spedizione', 50000.0, [[44, 38], [78, 38], [78, 48], [44, 48]], '#ef4444', [4, 1], 12500.0],
    ];

    public function run(): void
    {
        $warehouse = Warehouse::query()->firstOrCreate(
            ['code' => self::DEMO_CODE],
            [
                'name' => 'Magazzino demo',
                'description' => 'Impianto dimostrativo con aree di ricevimento, quarantena, stoccaggio, lavorazione e spedizione.',
                'map_width_m' => 80,
                'map_height_m' => 50,
                'is_active' => true,
            ],
        );

        foreach (self::AREAS as $code => [$type, $name, $maxKg, $polygon, $color, [$columns, $rows], $locationMaxKg]) {
            $area = Area::query()->firstOrCreate(
                ['warehouse_id' => $warehouse->id, 'code' => $code],
                [
                    'name' => $name,
                    'type' => $type,
                    'max_weight_kg' => $maxKg,
                    'map_geometry' => $polygon,
                    'color' => $color,
                    'is_active' => true,
                ],
            );

            $this->seedLocations($area, $polygon, $columns, $rows, $locationMaxKg);
        }
    }

    /**
     * Griglia di posizioni distribuite nel riquadro del poligono: `{area}-{colonna}{riga}` (es. ST-03B).
     *
     * @param  non-empty-list<array{0: int, 1: int}>  $polygon
     */
    private function seedLocations(Area $area, array $polygon, int $columns, int $rows, ?float $maxKg): void
    {
        $xs = array_column($polygon, 0);
        $ys = array_column($polygon, 1);
        $minX = min($xs);
        $minY = min($ys);
        $stepX = (max($xs) - $minX) / ($columns + 1);
        $stepY = (max($ys) - $minY) / ($rows + 1);

        for ($column = 1; $column <= $columns; $column++) {
            for ($row = 1; $row <= $rows; $row++) {
                $code = $area->code.'-'.str_pad((string) $column, 2, '0', STR_PAD_LEFT).\chr(64 + $row);

                Location::query()->firstOrCreate(
                    ['area_id' => $area->id, 'code' => $code],
                    [
                        'x' => round($minX + $stepX * $column, 2),
                        'y' => round($minY + $stepY * $row, 2),
                        'max_weight_kg' => $maxKg,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
