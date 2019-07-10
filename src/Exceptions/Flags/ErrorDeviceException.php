<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exception\Flags;

use RuntimeException;

class ErrorDeviceException extends RuntimeException
{
    /**
     * ErrorDeviceException constructor.
     */
    public function __construct()
    {
        parent::__construct("setserial: Error with device file", E_USER_WARNING);
    }
}