<?php
namespace Sanchescom\Serial\Systems;

trait Configure
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
}