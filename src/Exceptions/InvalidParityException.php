<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class InvalidParityException.
 */
class InvalidParityException extends RuntimeException
{
    /**
     * InvalidParityException constructor.
     *
     * @param string $parity
     */
    public function __construct(string $parity)
    {
        parent::__construct("Invalid parity: {$parity}.");
    }
}
