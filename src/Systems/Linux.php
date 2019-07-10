<?php

namespace Sanchescom\Serial\Systems;

class Linux extends AbstractSystem
{
    public function setBaudRate(int $rate)
    {
        $this->throwExceptionInvalidRate($rate);
        $this->throwExceptionInvalidDevice();

        $this->executor->command("stty -f {$this->device} {$rate}");
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
        if (preg_match("@^COM(\\d+):?$@i", $device, $matches)) {
            $device = "/dev/ttyS" . ($matches[1] - 1);
        }

        if ($this->executor->command("stty -F {$device}") === 0) {
            $this->device = $device;
        }
    }
}
