<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class InvalidDeviceException.
 */
class InvalidDeviceException extends RuntimeException
{
    /**
     * InvalidDeviceException constructor.
     */
    public function __construct()
    {
        parent::__construct("Invalid device.");
    }
}
