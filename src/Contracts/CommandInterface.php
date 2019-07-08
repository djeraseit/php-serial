<?php

namespace Sanchescom\Serial\Contracts;

/**
 * Interface CommandInterface.
 */
interface CommandInterface
{
    /**
     * @param string $command
     *
     * @return string
     */
    public function execute(string $command);
}
