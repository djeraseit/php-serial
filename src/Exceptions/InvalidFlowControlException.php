<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class InvalidFlowControlException.
 */
class InvalidFlowControlException extends RuntimeException
{
    /**
     * InvalidFlowControlException constructor.
     *
     * @param string $mode
     */
    public function __construct(string $mode)
    {
        parent::__construct("Invalid flow control mode specified: {$mode}.");
    }
}
