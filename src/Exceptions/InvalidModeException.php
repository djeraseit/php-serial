<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class InvalidModeException.
 */
class InvalidModeException extends RuntimeException
{
    /**
     * InvalidModeException constructor.
     *
     * @param string $mode
     */
    public function __construct(string $mode)
    {
        parent::__construct("Invalid opening mode : {$mode}. Use fopen() modes.");
    }
}
