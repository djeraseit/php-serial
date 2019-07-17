<?php

namespace Sanchescom\Serial\Systems;

/**
 * Class Windows.
 */
class Windows extends AbstractSystem
{
    /** @var array */
    protected static $flowControls = [
        "none" => "xon=off octs=off rts=on",
        "rts/cts" => "xon=off octs=on rts=hs",
        "xon/xoff" => "xon=on octs=off rts=on",
    ];

    /** @var string */
    protected static $utility = "mode";

    /** {@inheritdoc} */
    protected function executeBaudRate(int $rate)
    {
        $this->executor->command(self::$utility . " {$this->device} BAUD=" . self::$validBauds[$rate]);
    }

    /** {@inheritdoc} */
    protected function executeParity(int $parity)
    {
        $this->executor->command(self::$utility . " {$this->device} PARITY=" . $parity[0]);
    }

    /** {@inheritdoc} */
    protected function executeCharacterLength(int $length)
    {
        $this->executor->command(self::$utility . " {$this->device} DATA={$length}");
    }

    /** {@inheritdoc} */
    protected function executeStopBits(float $length)
    {
        $this->executor->command(self::$utility . " {$this->device} STOP={$length}");
    }

    /** {@inheritdoc} */
    protected function executeFlowControl(string $mode)
    {
        $this->executor->command(self::$utility . " {$this->device} " . self::$flowControls[$mode]);
    }

    /** {@inheritdoc} */
    protected function setDevice(string $device)
    {
        $this->executor->command(self::$utility . " {$device} xon=on BAUD=9600");

        if (preg_match("@^COM(\\d+):?$@i", $device, $matches) !== false) {
            $this->device = $device;
        }
    }

    /**
     * @param string $device
     * @param string $mode
     */
    protected function setHandel(string $device, string $mode)
    {
        $this->handel = fopen(strtolower($device), $mode);
    }
}
