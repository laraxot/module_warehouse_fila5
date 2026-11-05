<?php

declare(strict_types=1);

namespace Modules\Warehouse\Actions;

use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Normalizza il poligono di un'area (`map_geometry`): JSON o array `[[x,y],...]` in metri,
 * almeno 3 vertici, coordinate numeriche non negative. Stringa vuota/null = nessuna geometria.
 *
 * @phpstan-type Polygon list<array{0: float, 1: float}>
 */
class ParseMapGeometryAction
{
    use QueueableAction;

    public const int MIN_VERTICES = 3;

    /**
     * @return Polygon|null
     *
     * @throws \JsonException se la stringa non è JSON
     * @throws \InvalidArgumentException se la struttura non è un poligono valido
     */
    public function execute(mixed $geometry): ?array
    {
        if ($geometry === null || $geometry === '' || $geometry === []) {
            return null;
        }

        if (\is_string($geometry)) {
            $geometry = json_decode($geometry, true, 8, JSON_THROW_ON_ERROR);
        }

        $invalid = SafeStringCastAction::cast(__('warehouse::exceptions.geometry.invalid'));
        if (! \is_array($geometry) || ! array_is_list($geometry)) {
            throw new \InvalidArgumentException($invalid);
        }

        Assert::minCount($geometry, self::MIN_VERTICES, SafeStringCastAction::cast(__('warehouse::exceptions.geometry.too_few_vertices', ['min' => self::MIN_VERTICES])));

        $polygon = [];
        foreach ($geometry as $vertex) {
            if (! \is_array($vertex) || \count($vertex) !== 2 || ! isset($vertex[0], $vertex[1])
                || ! is_numeric($vertex[0]) || ! is_numeric($vertex[1])) {
                throw new \InvalidArgumentException($invalid);
            }

            $x = round((float) $vertex[0], 3);
            $y = round((float) $vertex[1], 3);
            if ($x < 0.0 || $y < 0.0) {
                throw new \InvalidArgumentException(SafeStringCastAction::cast(__('warehouse::exceptions.geometry.negative')));
            }

            $polygon[] = [$x, $y];
        }

        return $polygon;
    }

    /** Variante che non lancia eccezioni, per la validazione dei form. */
    public function isValid(mixed $geometry): bool
    {
        try {
            $this->execute($geometry);

            return true;
        } catch (\JsonException|\InvalidArgumentException) {
            return false;
        }
    }
}
