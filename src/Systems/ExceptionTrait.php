<?php

namespace Sanchescom\Serial\Systems;

use Sanchescom\Serial\Exceptions\ClosingException;
use Sanchescom\Serial\Exceptions\InvalidDeviceException;
use Sanchescom\Serial\Exceptions\InvalidFlowControlException;
use Sanchescom\Serial\Exceptions\InvalidHandleException;
use Sanchescom\Serial\Exceptions\InvalidModeException;
use Sanchescom\Serial\Exceptions\InvalidParityException;
use Sanchescom\Serial\Exceptions\InvalidRateException;
use Sanchescom\Serial\Exceptions\InvalidStopBitException;
use Sanchescom\Serial\Exceptions\SendingException;

trait ExceptionTrait
{
    protected function throwExceptionInvalidRate($rate)
    {
        if (!isset(self::$validBauds[$rate])) {
            throw new InvalidRateException($rate);
        }
    }

    protected function throwExceptionInvalidParity($parity)
    {
        if (!isset(self::$partyArgs[$parity])) {
            throw new InvalidParityException($parity);
        }
    }

    protected function throwExceptionInvalidFlowControl($mode)
    {
        if (!isset(self::$flowControls[$mode])) {
            throw new InvalidFlowControlException($mode);
        }
    }

    protected function throwExceptionStopBit($length)
    {
        if (!in_array($length, self::$validStopBitsLength)) {
            throw new InvalidStopBitException($length);
        }
    }

    protected function throwExceptionInvalidMode($mode)
    {
        if (!preg_match("@^[raw]\\+?b?$@", $mode)) {
            throw new InvalidModeException($mode);
        }
    }

    protected function throwExceptionClosing($pointer)
    {
        if ($pointer === false) {
            throw new ClosingException();
        }
    }

    protected function throwExceptionSending($pointer)
    {
        if ($pointer === false) {
            throw new SendingException();
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
