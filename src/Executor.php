<?php

namespace Sanchescom\Serial;

use Sanchescom\Serial\Contracts\ExecutorInterface;
use Sanchescom\Serial\Exceptions\CommandException;

/**
 * Class CommandExecutor.
 */
class Executor implements ExecutorInterface
{
    /** {@inheritdoc} */
    public function command(string $command)
    {
        $desc = [
            1 => ["pipe", "w"],
            2 => ["pipe", "w"],
        ];

        $process = proc_open($command, $desc, $pipes);

        $return = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $result = proc_close($process);

        if ($result !== 0) {
            throw new CommandException($command, $error, $return);
        }

        return $return;
    }

    /** {@inheritdoc} */
    public function program(string $command)
    {
        $command .= ' 2>&1';

        exec($command, $output, $code);

        $output = count($output) === 0
            ? $code
            : implode(PHP_EOL, $output);

        if ($code !== 0) {
            throw new CommandException($command, $output, $code);
        }

        return $output;
    }
}
