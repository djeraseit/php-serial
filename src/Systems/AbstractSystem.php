<?php

namespace Sanchescom\Serial\Systems;

use Sanchescom\Serial\Contracts\ConfigInterface;
use Sanchescom\Serial\Contracts\ExecutorInterface;
use Sanchescom\Serial\Contracts\SystemInterface;
use Sanchescom\Serial\Exceptions\ClosingException;
use Sanchescom\Serial\Exceptions\SendingException;

/**
 * Class AbstractSystem.
 */
abstract class AbstractSystem implements ConfigInterface, SystemInterface
{
    use ExceptionTrait;

    /** @var int */
    const DEFAULT_READ_INDEX = 0;

    /** @var int */
    const DEFAULT_READ_LENGTH = 128;

    /** @var int */
    const MIN_CHARACTER_LENGTH = 5;

    /** @var int */
    const MAX_CHARACTER_LENGTH = 8;

    /** @var array */
    protected static $flowControls = [];

    /** @var array */
    protected static $validStopBitsLength = [
        1,
        1.5,
        2,
    ];

    /** @var array */
    protected static $validBauds = [
        110 => 11,
        150 => 15,
        300 => 30,
        600 => 60,
        1200 => 12,
        2400 => 24,
        4800 => 48,
        9600 => 96,
        19200 => 19,
        38400 => 38400,
        57600 => 57600,
        115200 => 115200,
    ];

    protected static $partyArgs = [
        "none" => "-parenb",
        "odd" => "parenb parodd",
        "even" => "parenb -parodd",
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

        $this->setHandel($this->device, $mode);

        return $this->setBlockingMode();
    }

    /** {@inheritdoc} */
    public function close()
    {
        $this->throwExceptionInvalidHandle();

        if (fclose($this->handel) === false) {
            throw new ClosingException();
        }

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

        usleep((int)($waitForReply * 1000000));
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

        if (fwrite($this->handel, $this->buffer) === false) {
            throw new SendingException();
        }

        $this->clearBuffer();
    }

    /**
     * Set blocking/non-blocking mode on a stream.
     *
     * @param int $mode
     *
     * @return bool
     */
    public function setBlockingMode(int $mode = 0)
    {
        $this->throwExceptionInvalidHandle();

        return stream_set_blocking($this->handel, $mode);
    }

    /** {@inheritdoc} */
    public function setBaudRate(int $rate)
    {
        $this->throwExceptionInvalidDevice();

        $this->throwExceptionInvalidRate($rate);

        $this->executeBaudRate($rate);
    }

    /** {@inheritdoc} */
    public function setParity($parity)
    {
        $this->throwExceptionInvalidDevice();

        $this->throwExceptionInvalidParity($parity);

        $this->executeParity($parity);
    }

    /** {@inheritdoc} */
    public function setCharacterLength(int $length)
    {
        $this->throwExceptionInvalidDevice();

        if ($length < self::MIN_CHARACTER_LENGTH) {
            $length = self::MIN_CHARACTER_LENGTH;
        }

        if ($length > self::MAX_CHARACTER_LENGTH) {
            $length = self::MAX_CHARACTER_LENGTH;
        }

        $this->executeCharacterLength($length);
    }

    /** {@inheritdoc} */
    public function setStopBits($length)
    {
        $this->throwExceptionInvalidDevice();

        $this->throwExceptionStopBit($length);

        $this->executeStopBits($length);
    }

    /** {@inheritdoc} */
    public function setFlowControl($mode)
    {
        $this->throwExceptionInvalidDevice();

        $this->throwExceptionInvalidFlowControl($mode);

        $this->executeFlowControl($mode);
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
     * @param string $device
     * @param string $mode
     *
     * @return mixed
     */
    abstract protected function setHandel(string $device, string $mode);

    /**
     * @param int $rate
     *
     * @return mixed
     */
    abstract protected function executeBaudRate(int $rate);

    /**
     * @param int $parity
     *
     * @return mixed
     */
    abstract protected function executeParity(int $parity);

    /**
     * @param int $length
     *
     * @return mixed
     */
    abstract protected function executeCharacterLength(int $length);

    /**
     * @param float $length
     *
     * @return mixed
     */
    abstract protected function executeStopBits(float $length);

    /**
     * @param string $mode
     *
     * @return mixed
     */
    abstract protected function executeFlowControl(string $mode);

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
