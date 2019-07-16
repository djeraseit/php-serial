<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class InvalidHandleException.
 */
class InvalidHandleException extends RuntimeException
{
    /**
     * InvalidHandleException constructor.
     */
    public function __construct()
    {
        parent::__construct("Invalid handle.");
    }
}
