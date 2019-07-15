<?php

namespace Sanchescom\Serial\Systems;

class Windows extends AbstractSystem
{
    protected static $flowControls = [
        "none"     => "xon=off octs=off rts=on",
        "rts/cts"  => "xon=off octs=on rts=hs",
        "xon/xoff" => "xon=on octs=off rts=on",
    ];

    protected function executeBaudRate(int $rate)
    {
        $this->executor->command("mode {$this->device} BAUD=".self::$validBauds[$rate]);
    }

    protected function executeParity(int $parity)
    {
        $this->executor->command( "mode {$this->device} PARITY=".$parity[0]);
    }

    protected function executeCharacterLength(int $length)
    {
        $this->executor->command("mode {$this->device} DATA={$length}");
    }

    protected function executeStopBits(float $length)
    {
        $this->executor->command("mode {$this->device} STOP={$length}");
    }

    protected function executeFlowControl(string $mode)
    {
        $this->executor->command("mode {$this->device} ".self::$flowControls[$mode]);
    }

    /** {@inheritdoc}*/
    protected function setDevice(string $device)
    {
        if (preg_match("@^COM(\\d+):?$@i", $device, $matches) &&
            $this->executor->command("mode {$device} xon=on BAUD=9600") === 0
        ) {
            $this->device = "COM" . $matches[1];
        }
    }

    /**
     * @param string $device
     * @param string $mode
     */
    protected function setHandel(string $device, string $mode)
    {
        $this->handel = fopen("\\." . strtolower($device), $mode);
    }
}
