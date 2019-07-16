<?php

namespace Sanchescom\Serial\Contracts;

/**
 * Interface CommandInterface.
 */
interface ExecutorInterface
{
    /**
     * @param string $command
     *
     * @return string
     */
    public function command(string $command);

    /**
     * @param string $command
     *
     * @return mixed
     */
    public function program(string $command);
}
