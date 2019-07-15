<?php

namespace Sanchescom\Serial\Systems;

class Darwin extends AbstractSystem
{
    use UnixTrait;

    protected function executeBaudRate(int $rate)
    {
        $this->executor->command("stty -f {$this->device} {$rate}");
    }

    protected function executeParity(int $parity)
    {
        $this->executor->command("stty -f {$this->device} " . self::$partyArgs[$parity]);
    }

    protected function executeCharacterLength(int $length)
    {
        $this->executor->command("stty -f {$this->device} cs {$length}");
    }

    protected function executeStopBits(float $length)
    {
        $prefix = (($length == 1) ? "-" : "");

        $this->executor->command("stty -f {$this->device} {$prefix}cstopb");
    }

    protected function executeFlowControl(string $mode)
    {
        $this->executor->command("stty -f {$this->device} " . self::$flowControls[$mode]);
    }

    /** {@inheritdoc} */
    protected function setDevice(string $device)
    {
        if ($this->executor->command("stty -f {$device}") === 0) {
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
}
