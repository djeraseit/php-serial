<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class ClosingException.
 */
class ClosingException extends RuntimeException
{
    /**
     * ClosingException constructor.
     */
    public function __construct()
    {
        parent::__construct("Closing error.");
    }
}
