<?php

namespace Sanchescom\Serial\Systems;

class Windows extends AbstractSystem
{
    public function setBaudRate(int $rate)
    {
        $this->throwExceptionInvalidRate($rate);
        $this->throwExceptionInvalidDevice();

        $this->executor->command("mode {$this->device} BAUD=" . self::$validBauds[$rate]);
    }

    public function setParity($parity)
    {
        $this->throwExceptionInvalidDevice();
    }

    public function setCharacterLength($int)
    {
        $this->throwExceptionInvalidDevice();
    }

    public function setStopBits($length)
    {
        $this->throwExceptionInvalidDevice();
    }

    public function setFlowControl($mode)
    {
        $this->throwExceptionInvalidDevice();
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
}
