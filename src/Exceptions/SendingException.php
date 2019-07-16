<?php

declare(strict_types=1);

namespace Sanchescom\Serial\Exceptions;

use RuntimeException;

/**
 * Class SendingException.
 */
class SendingException extends RuntimeException
{
    /**
     * SendingException constructor.
     */
    public function __construct()
    {
        parent::__construct("Sending message error.");
    }
}
