<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Modules\Warehouse\Models\StockUnit;
use Modules\Warehouse\Models\Warehouse;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Genera il prossimo codice unità `SU-{anno}-{progressivo 6 cifre}`.
 *
 * Va chiamata dentro una transazione (CreateStockUnitAction): la riga del magazzino e
 * l'ultimo codice dell'anno vengono letti con `lockForUpdate`, così due creazioni
 * concorrenti non ottengono lo stesso numero. Il progressivo riparte ogni anno e
 * considera anche le unità eliminate (un codice stampato su un'etichetta non si riusa).
 *
 * `stock_units.code` è univoco su tutta l'installazione ed è il codice del QR di Labeling:
 * per questo il progressivo è per anno su tutti i magazzini (vedi docs/README.md).
 */
class GenerateStockUnitCodeAction
{
    use QueueableAction;

    public const string PREFIX = 'SU';

    public const int DIGITS = 6;

    public function execute(Warehouse $warehouse, ?CarbonImmutable $at = null): string
    {
        $year = ($at ?? CarbonImmutable::now())->year;

        Warehouse::query()->whereKey($warehouse->getKey())->lockForUpdate()->firstOrFail();

        $last = StockUnit::withTrashed()
            ->where('code', 'like', self::yearPrefix($year).'%')
            ->orderByDesc('code')
            ->lockForUpdate()
            ->value('code');

        return self::format($year, self::nextNumber(\is_string($last) ? $last : null, $year));
    }

    public static function format(int $year, int $number): string
    {
        Assert::range($number, 1, (10 ** self::DIGITS) - 1, 'Progressivo unità fuori intervallo: %s');

        return self::yearPrefix($year).str_pad((string) $number, self::DIGITS, '0', STR_PAD_LEFT);
    }

    /** Numero successivo all'ultimo codice dell'anno (1 se non ce ne sono). */
    public static function nextNumber(?string $lastCode, int $year): int
    {
        if ($lastCode === null || ! Str::startsWith($lastCode, self::yearPrefix($year))) {
            return 1;
        }

        $digits = Str::after($lastCode, self::yearPrefix($year));
        Assert::regex($digits, '/^\d+$/', 'Codice unità non conforme: '.$lastCode);

        return ((int) $digits) + 1;
    }

    public static function yearPrefix(int $year): string
    {
        return self::PREFIX.'-'.$year.'-';
    }
}
