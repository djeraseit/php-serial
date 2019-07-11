<?php

namespace Sanchescom\Serial\Systems;

class Linux extends AbstractSystem
{
    protected function executeBaudRate(int $rate)
    {
        $this->executor->command("stty -F {$this->device} {$rate}");
    }

    protected function executeCharacterLength(int $length)
    {
        $this->executor->command("stty -F {$this->device} cs {$length}");
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
