<?php

namespace Sanchescom\Serial\Systems;

use RuntimeException;
use Sanchescom\Serial\Contracts\DeviceInterface;
use Sanchescom\Serial\Contracts\ExecutorInterface;
use Sanchescom\Serial\Contracts\SystemInterface;
use Sanchescom\Serial\Exceptions\ClosingException;
use Sanchescom\Serial\Exceptions\InvalidDeviceException;
use Sanchescom\Serial\Exceptions\InvalidHandleException;
use Sanchescom\Serial\Exceptions\InvalidModeException;
use Sanchescom\Serial\Exceptions\InvalidRateException;
use Sanchescom\Serial\Exceptions\SendingException;

abstract class AbstractSystem implements DeviceInterface, SystemInterface
{
    /** @var array */
    protected static $validBauds = [
        110    => 11,
        150    => 15,
        300    => 30,
        600    => 60,
        1200   => 12,
        2400   => 24,
        4800   => 48,
        9600   => 96,
        19200  => 19,
        38400  => 38400,
        57600  => 57600,
        115200 => 115200,
    ];

    /** @var \Sanchescom\Serial\Contracts\ExecutorInterface */
    protected $executor;

    /** @var string */
    protected $device;

    /** @var string */
    protected $buffer;

    /** @var mixed */
    protected $handel;

    /**
     * This var says if buffer should be flushed by sendMessage (true) or manually (false)
     *
     * @var bool
     */
    protected $autoFlush = true;

    /**
     * AbstractOperationSystem constructor.
     *
     * @param \Sanchescom\Serial\Contracts\ExecutorInterface $executor
     * @param string $device
     */
    public function __construct(ExecutorInterface $executor, string $device)
    {
        $this->setExecutor($executor);
        $this->setDevice($device);
    }

    /** {@inheritdoc} */
    public function open(string $mode = "r+b")
    {
        $this->throwExceptionInvalidMode($mode);

        $this->throwExceptionInvalidDevice();

        $this->setHandel(fopen($this->device, $mode));

        $this->throwExceptionInvalidHandle();

        return stream_set_blocking($this->handel, 0);
    }

    /** {@inheritdoc} */
    public function close()
    {
        $this->throwExceptionInvalidHandle();

        $this->throwExceptionClosing(fclose($this->handel));

        $this->unsetHandle();

        return true;
    }

    /** {@inheritdoc} */
    public function send(string $message, float $waitForReply = 0.1)
    {
        $this->buffer .= $message;

        if ($this->autoFlush) {
            $this->flush();
        }

        usleep((int) ($waitForReply * 1000000));
    }

    /** {@inheritdoc} */
    public function read(int $count = 0)
    {
        $this->throwExceptionInvalidHandle();

        $content = "";

        $i = 0;

        if ($count !== 0) {
            do {
                if ($i > $count) {
                    $content .= fread($this->handel, ($count - $i));
                } else {
                    $content .= fread($this->handel, 128);
                }
            } while (($i += 128) === strlen($content));
        } else {
            do {
                $content .= fread($this->handel, 128);
            } while (($i += 128) === strlen($content));
        }

        return $content;
    }

    /** {@inheritdoc} */
    public function flush()
    {
        $this->throwExceptionInvalidHandle();

        $this->throwExceptionSending(fwrite($this->handel, $this->buffer));

        $this->clearBuffer();
    }

    /**
     * Set a setserial parameter (cf man setserial)
     * NO MORE USEFUL !
     * 	-> No longer supported
     * 	-> Only use it if you need it
     *
     * @param  string $param parameter name
     * @param  string $arg   parameter value
     *
     * @return bool
    */
    public function setSerialFlag($param, $arg = "")
    {
        $this->throwExceptionInvalidDevice();

        $return = $this->executor->program("setserial {$this->device} {$param} {$arg}");

        if ($return[0] === "I") {
            throw new RuntimeException("setserial: Invalid flag", E_USER_WARNING);
        } elseif ($return[0] === "/") {
            throw new RuntimeException("setserial: Error with device file", E_USER_WARNING);
        }

        return true;
    }

    /**
     * Device set function : used to set the device name/address.
     * -> linux : use the device address, like /dev/ttyS0
     * -> osx : use the device address, like /dev/tty.serial
     * -> windows : use the COMxx device name, like COM1 (can also be used with linux)
     *
     * @param  string $device the name of the device to be used
     *
     * @return void
     */
    abstract protected function setDevice(string $device);

    /**
     * @param mixed $handel
     */
    protected function setHandel($handel)
    {
        $this->handel = $handel;
    }

    /**
     * @return void
     */
    protected function clearBuffer()
    {
        $this->buffer = "";
    }

    /**
     * @return void
     */
    protected function unsetHandle()
    {
        $this->handel = null;
    }

    /**
     * @param ExecutorInterface $executor
     */
    protected function setExecutor(ExecutorInterface $executor): void
    {
        $this->executor = $executor;
    }

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
}
