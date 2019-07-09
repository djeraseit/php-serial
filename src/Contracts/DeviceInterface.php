<?php

namespace Sanchescom\Serial\Contracts;

interface DeviceInterface
{
    public function setBaudRate($rate);

    public function setParity($parity);

    public function setCharacterLength($int);

    public function setStopBits($length);

    public function setFlowControl($mode);
}