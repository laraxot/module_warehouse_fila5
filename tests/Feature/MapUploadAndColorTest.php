<?php

declare(strict_types=1);

use Modules\Warehouse\Datas\MapAreaData;
use Modules\Warehouse\Datas\OccupancyData;
use Modules\Warehouse\Enums\AreaTypeEnum;
use Modules\Warehouse\Exceptions\InvalidAreaColorException;
use Modules\Warehouse\Models\Area;
use Modules\Warehouse\Models\Warehouse;
use Modules\Warehouse\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

test('the floor plan accepts only raster images, never SVG', function (): void {
    $warehouse = Warehouse::factory()->createOne();
    $warehouse->registerMediaCollections();

    $collection = collect($warehouse->mediaCollections)->firstWhere('name', Warehouse::MAP_COLLECTION);

    Assert::assertNotNull($collection);
    Assert::assertSame(['image/png', 'image/jpeg', 'image/webp'], $collection->acceptsMimeTypes);
    Assert::assertNotContains('image/svg+xml', Warehouse::MAP_MIME_TYPES);
});

test('an area colour must be #rrggbb', function (string $color): void {
    Area::factory()->createOne(['color' => $color]);
})->with([
    'css injection' => ['red; background:url(https://evil.test/x)'],
    'named colour' => ['red'],
    'short hex' => ['#fff'],
    'rgb' => ['rgb(1,2,3)'],
])->throws(InvalidAreaColorException::class);

test('a valid hex colour, null or empty string are accepted', function (): void {
    Assert::assertSame('#22c55e', Area::factory()->createOne(['color' => '#22c55e'])->color);
    Assert::assertNull(Area::factory()->createOne(['color' => null])->color);
    Assert::assertNull(Area::factory()->createOne(['color' => ''])->refresh()->color);
});

test('the map never writes an invalid stored colour into the style attribute', function (): void {
    $data = static fn (?string $color): MapAreaData => new MapAreaData(
        id: 1,
        code: 'A',
        name: 'A',
        type: AreaTypeEnum::STORAGE,
        polygon: [[0.0, 0.0], [1.0, 0.0], [1.0, 1.0]],
        color: $color,
        occupancy: new OccupancyData(0.0, null),
        unitsCount: 0,
    );

    Assert::assertSame('#0ea5e9', $data('#0ea5e9')->strokeColor('var(--gray-500)'));
    Assert::assertSame('var(--gray-500)', $data('red;fill:url(x)')->strokeColor('var(--gray-500)'));
    Assert::assertSame('var(--gray-500)', $data(null)->strokeColor('var(--gray-500)'));
});
