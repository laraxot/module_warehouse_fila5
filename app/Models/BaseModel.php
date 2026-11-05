<?php

declare(strict_types=1);

namespace Modules\Warehouse\Models;

use Modules\Xot\Models\XotBaseModel;

/**
 * Base dei modelli del modulo Warehouse (connessione `warehouse`).
 *
 * XotBaseModel fornisce HasXotFactory, RelationX e Updater (created_by/updated_by).
 * Le chiavi primarie del modulo sono bigIncrements: `id` torna intero.
 */
abstract class BaseModel extends XotBaseModel
{
    /** @var string */
    protected $connection = 'warehouse';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'id' => 'integer',
        ]);
    }
}
