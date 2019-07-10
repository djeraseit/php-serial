<?php

namespace Sanchescom\Serial\Systems;

use Sanchescom\Serial\Exception\Flags\ErrorDeviceException;
use Sanchescom\Serial\Exception\Flags\InvalidFlagException;
use Sanchescom\Serial\Exceptions\ClosingException;
use Sanchescom\Serial\Exceptions\InvalidDeviceException;
use Sanchescom\Serial\Exceptions\InvalidHandleException;
use Sanchescom\Serial\Exceptions\InvalidModeException;
use Sanchescom\Serial\Exceptions\InvalidRateException;
use Sanchescom\Serial\Exceptions\SendingException;

trait HasThrows
{
    protected function throwExceptionInvalidRate($rate)
    {
        if (!isset(self::$validBauds[$rate])) {
            throw new InvalidRateException($rate);
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

    protected function throwExceptionInvalidFlag(string $return)
    {
        if ($return[0] === "I") {
            throw new InvalidFlagException();
        }
    }

    protected function throwExceptionErrorDevice(string $return)
    {
        if ($return[0] === "/") {
            throw new ErrorDeviceException();
        }
    }
}
