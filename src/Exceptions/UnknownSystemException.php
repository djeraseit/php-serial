<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class UnknownSystemException.
 */
class UnknownSystemException extends RuntimeException
{
    /**
     * UnknownSystemException constructor.
     */
    public function __construct()
    {
        parent::__construct("Operation system doesn't support: " . PHP_OS_FAMILY);
    }
}
