<?php

namespace Sanchescom\Serial\Contracts;

interface DeviceInterface
{
    /**
     * Configure the Baud Rate
     * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
     * 57600 and 115200.
     *
     * @param  int  $rate the rate to set the port in
     * @return bool
     */
    public function setBaudRate(int $rate);

    public function setParity($parity);

    public function setCharacterLength(int $length);

    public function setStopBits($length);

    public function setFlowControl($mode);
}