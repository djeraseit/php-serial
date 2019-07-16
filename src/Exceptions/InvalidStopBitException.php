<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class InvalidStopBitsLengthException.
 */
class InvalidStopBitException extends RuntimeException
{
    /**
     * InvalidStopBitsLengthException constructor.
     *
     * @param float $length
     */
    public function __construct(float $length)
    {
        parent::__construct("Specified stop bit length is invalid: {$length}.");
    }
}
