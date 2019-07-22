<?php

namespace Sanchescom\Serial\Systems;

use Sanchescom\Serial\Exceptions\InvalidDeviceException;
use Sanchescom\Serial\Exceptions\InvalidFlowControlException;
use Sanchescom\Serial\Exceptions\InvalidHandleException;
use Sanchescom\Serial\Exceptions\InvalidModeException;
use Sanchescom\Serial\Exceptions\InvalidParityException;
use Sanchescom\Serial\Exceptions\InvalidRateException;
use Sanchescom\Serial\Exceptions\InvalidStopBitException;

trait ExceptionTrait
{
    protected function throwExceptionInvalidRate($rate)
    {
        if (!isset(static::$validBauds[$rate])) {
            throw new InvalidRateException($rate);
        }
    }

    protected function throwExceptionInvalidParity($parity)
    {
        if (!isset(static::$partyArgs[$parity])) {
            throw new InvalidParityException($parity);
        }
    }

    protected function throwExceptionInvalidFlowControl($mode)
    {
        if (!isset(static::$flowControls[$mode])) {
            throw new InvalidFlowControlException($mode);
        }
    }

    protected function throwExceptionStopBit($length)
    {
        if (!in_array($length, static::$validStopBitsLength)) {
            throw new InvalidStopBitException($length);
        }
    }

    protected function throwExceptionInvalidMode($mode)
    {
        if (!preg_match("@^[raw]\\+?b?$@", $mode)) {
            throw new InvalidModeException($mode);
        }
    }

    protected function throwExceptionInvalidHandle()
    {
        if (!$this->handel) {
            throw new InvalidHandleException();
        }
    }

    protected function throwExceptionInvalidDevice()
    {
        if (!$this->device) {
            throw new InvalidDeviceException();
        }
    }
}
