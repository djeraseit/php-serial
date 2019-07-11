<?php

namespace Sanchescom\Serial\Systems;

use Sanchescom\Serial\Contracts\DeviceInterface;
use Sanchescom\Serial\Contracts\ExecutorInterface;
use Sanchescom\Serial\Contracts\SystemInterface;

abstract class AbstractSystem implements DeviceInterface, SystemInterface
{
    use HasThrows;

    /** @var int */
    const DEFAULT_READ_INDEX = 0;

    /** @var int */
    const DEFAULT_READ_LENGTH = 128;

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
        $length = self::DEFAULT_READ_LENGTH;
        $index = self::DEFAULT_READ_INDEX;

        do {
            if ($count !== self::DEFAULT_READ_INDEX && $index > $count) {
                $length = $count - $index;
            }

            $content .= fread($this->handel, $length);
        } while (($index += self::DEFAULT_READ_LENGTH) === strlen($content));

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
     * @return void
    */
    public function setSerialFlag($param, $arg = "")
    {
        $this->throwExceptionInvalidDevice();

        $return = $this->executor->program("setserial {$this->device} {$param} {$arg}");

        $this->throwExceptionInvalidFlag($return);

        $this->throwExceptionErrorDevice($return);
    }

    public function setBaudRate(int $rate)
    {
        $this->throwExceptionInvalidRate($rate);
        $this->throwExceptionInvalidDevice();

        $this->executeBaudRate($rate);
    }

    public function setParity($parity)
    {
        $this->throwExceptionInvalidDevice();
    }

    public function setCharacterLength(int $length)
    {
        $this->throwExceptionInvalidDevice();

        if ($length < 5) {
            $length = 5;
        }

        if ($length > 8) {
            $length = 8;
        }

        $this->executeCharacterLength($length);
    }

    public function setStopBits($length)
    {
        $this->throwExceptionInvalidDevice();
    }

    public function setFlowControl($mode)
    {
        $this->throwExceptionInvalidDevice();
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

    abstract protected function executeBaudRate(int $rate);

    abstract protected function executeCharacterLength(int $length);

    /**
     * @param mixed $handel
     */
    protected function setHandel($handel)
    {
        $this->handel = $handel;
    }

    /**
     * @param ExecutorInterface $executor
     */
    protected function setExecutor(ExecutorInterface $executor): void
    {
        $this->executor = $executor;
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
}
