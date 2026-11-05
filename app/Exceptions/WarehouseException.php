<?php

declare(strict_types=1);

namespace Modules\Warehouse\Exceptions;

use DomainException;

/**
 * Base delle eccezioni di dominio del modulo Warehouse.
 *
 * Il messaggio è già tradotto (lang `warehouse::exceptions`): le Filament actions lo
 * mostrano così com'è nella notifica di errore.
 */
abstract class WarehouseException extends DomainException {}
