<?php

namespace Sanchescom\Serial\Systems;

use Sanchescom\Serial\Contracts\CommandInterface;

abstract class AbstractOperationSystem
{
    /** @var int */
    const SERIAL_DEVICE_NOT_SET = 0;

    /** @var int */
    const SERIAL_DEVICE_SET = 1;

    /** @var int */
    const SERIAL_DEVICE_OPENED = 2;

    /** @var int */
    protected $state = self::SERIAL_DEVICE_NOT_SET;

    /** @var \Sanchescom\Serial\Contracts\CommandInterface */
    protected $command;

    /**
     * AbstractNetworks constructor.
     *
     * @param \Sanchescom\Serial\Contracts\CommandInterface $command
     */
    public function __construct(CommandInterface $command)
    {
        $this->command = $command;
    }

    /**
     * @throws \Exception
     *
     * @return \Sanchescom\Serial\Systems\AbstractOperationSystem
     */
    public function setDevice(string $device)
    {
        return $this;
    }
}