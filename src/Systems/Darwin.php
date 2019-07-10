<?php

namespace Sanchescom\Serial\Systems;

class Darwin extends AbstractSystem
{
    public function setBaudRate($rate)
    {
        $this->throwExceptionInvalidRate($rate);
        $this->throwExceptionInvalidDevice();

        $this->executor->command("stty -F {$this->device} {$rate}");
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
        if ($this->executor->command("stty -f {$device}") === 0) {
            $this->device = $device;
        }
    }
}
