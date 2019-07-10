<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class InvalidRateException.
 */
class InvalidRateException extends RuntimeException
{
    /**
     * InvalidRateException constructor.
     *
     * @param string $rate
     */
    public function __construct(string $rate)
    {
        parent::__construct("Invalid bound rate : {$rate}.");
    }
}
