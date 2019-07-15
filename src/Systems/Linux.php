<?php

namespace Sanchescom\Serial\Systems;

use Sanchescom\Serial\Exceptions\InvalidStopBitException;

class Linux extends AbstractSystem
{
    use UnixTrait;

    const INVALID_STOP_BIT_LENGTH = 1.5;

    protected function executeBaudRate(int $rate)
    {
        $this->executor->command("stty -F {$this->device} {$rate}");
    }

    protected function executeParity(int $parity)
    {
        $this->executor->command("stty -F {$this->device} " . self::$partyArgs[$parity]);
    }

    protected function executeCharacterLength(int $length)
    {
        $this->executor->command("stty -F {$this->device} cs {$length}");
    }

    protected function executeStopBits(float $length)
    {
        $prefix = (($length == 1) ? "-" : "");

        $this->executor->command("stty -F {$this->device} {$prefix}cstopb");
    }

    protected function executeFlowControl(string $mode)
    {
        $this->executor->command("stty -F {$this->device} " . self::$flowControls[$mode]);
    }

    /** {@inheritdoc} */
    protected function setDevice(string $device)
    {
        if (preg_match("@^COM(\\d+):?$@i", $device, $matches)) {
            $device = "/dev/ttyS" . ($matches[1] - 1);
        }

        if ($this->executor->command("stty -F {$device}") === 0) {
            $this->device = $device;
        }
    }

    /**
     * @param string $device
     * @param string $mode
     */
    protected function setHandel(string $device, string $mode)
    {
        $this->handel = fopen($device, $mode);
    }

    protected function throwExceptionStopBitsLength($length)
    {
        if ($length == self::INVALID_STOP_BIT_LENGTH) {
            throw new InvalidStopBitException($length);
        }
    }
}
