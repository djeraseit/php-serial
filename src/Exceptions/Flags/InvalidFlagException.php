<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exception\Flags;

use RuntimeException;

class InvalidFlagException extends RuntimeException
{
    /**
     * InvalidFlagException constructor.
     */
    public function __construct()
    {
        parent::__construct("setserial: Invalid flag", E_USER_WARNING);
    }
}