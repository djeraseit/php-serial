<?php

namespace Sanchescom\Serial\Systems;

/**
 * Class Darwin.
 */
class Darwin extends AbstractSystem
{
    use UnixTrait;

    /** {@inheritdoc} */
    protected function executeBaudRate(int $rate)
    {
        $this->executor->command(self::$utility . " -f {$this->device} {$rate}");
    }

    /** {@inheritdoc} */
    protected function executeParity(int $parity)
    {
        $this->executor->command(self::$utility . " -f {$this->device} " . self::$partyArgs[$parity]);
    }

    /** {@inheritdoc} */
    protected function executeCharacterLength(int $length)
    {
        $this->executor->command(self::$utility . " -f {$this->device} cs {$length}");
    }

    /** {@inheritdoc} */
    protected function executeStopBits(float $length)
    {
        $prefix = (($length == 1) ? "-" : "");

        $this->executor->command(self::$utility . " -f {$this->device} {$prefix}cstopb");
    }

    /** {@inheritdoc} */
    protected function executeFlowControl(string $mode)
    {
        $this->executor->command(self::$utility . " -f {$this->device} " . self::$flowControls[$mode]);
    }

    /** {@inheritdoc} */
    protected function setDevice(string $device)
    {
        if ($this->executor->command(self::$utility . " -f {$device} --version") === 0) {
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
