<?php

namespace Sanchescom\Serial;

use Sanchescom\Serial\Contracts\ExecutorInterface;
use Sanchescom\Serial\Exceptions\UnknownSystemException;
use Sanchescom\Serial\Systems\AbstractSystem;
use Sanchescom\Serial\Systems\Darwin;
use Sanchescom\Serial\Systems\Linux;
use Sanchescom\Serial\Systems\Windows;

/**
 * Class Serial.
 */
class Serial
{
    /** @var string */
    const OS_LINUX = 'Linux';

    /** @var string */
    const OS_DARWIN = 'Darwin';

    /** @var string */
    const OS_WINDOWS = 'Windows';

    /** @var string */
    protected static $executorClass = Executor::class;

    /** @var string */
    protected static $phpOperationSystem = PHP_OS_FAMILY;

    /** @var array */
    protected static $systems = [
        self::OS_LINUX => Linux::class,
        self::OS_DARWIN => Darwin::class,
        self::OS_WINDOWS => Windows::class,
    ];

    /**
     * @param string $device
     *
     * @throws \Exception
     *
     * @return AbstractSystem
     */
    public function setDevice(string $device)
    {
        return $this->getSystemInstance($device);
    }

    /**
     * @param string $executorClass
     */
    public static function setExecutorClass(string $executorClass): void
    {
        self::$executorClass = $executorClass;
    }

    /**
     * @param string $phpOperationSystem
     */
    public static function setPhpOperationSystem(string $phpOperationSystem): void
    {
        self::$phpOperationSystem = $phpOperationSystem;
    }

    /**
     * Getting instance on network collections depended on operation system.
     *
     * @param string $device
     *
     * @return \Sanchescom\Serial\Systems\AbstractSystem
     */
    protected function getSystemInstance(string $device): AbstractSystem
    {
        if (!array_key_exists(static::$phpOperationSystem, static::$systems)) {
            throw new UnknownSystemException();
        }

        return new static::$systems[static::$phpOperationSystem]($this->getCommandInstance(), $device);
    }

    /**
     * @return \Sanchescom\Serial\Contracts\ExecutorInterface
     */
    protected function getCommandInstance(): ExecutorInterface
    {
        return new static::$executorClass();
    }

    /**
     * Configure parity.
     * Modes : odd, even, none
     *
     * @param  string $parity one of the modes
     * @return bool
     */
    public function confParity($parity)
    {
        $args = array(
            "none" => "-parenb",
            "odd"  => "parenb parodd",
            "even" => "parenb -parodd",
        );

        if (!isset($args[$parity])) {
            trigger_error("Parity mode not supported", E_USER_WARNING);

            return false;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec(
                "stty -F " . $this->_device . " " . $args[$parity],
                $out
            );
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec(
                "stty -f " . $this->_device . " " . $args[$parity],
                $out
            );
        } else {
            $ret = $this->_exec(
                "mode " . $this->_winDevice . " PARITY=" . $parity{0},
                $out
            );
        }
    }

    /**
     * Sets the length of a character.
     *
     * @param  int  $length length of a character (5 <= length <= 8)
     * @return bool
     */
    public function confCharacterLength(int $length)
    {


        if ($this->_os === "linux") {
            $ret = $this->_exec(
                "stty -F " . $this->_device . " cs" . $length,
                $out
            );
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec(
                "stty -f " . $this->_device . " cs" . $length,
                $out
            );
        } else {
            $ret = $this->_exec(
                "mode " . $this->_winDevice . " DATA=" . $length,
                $out
            );
        }
    }

    /**
     * Sets the length of stop bits.
     *
     * @param  float $length the length of a stop bit. It must be either 1,
     *                       1.5 or 2. 1.5 is not supported under linux and on
     *                       some computers.
     * @return bool
     */
    public function confStopBits($length)
    {
        if ($length != 1 && $length != 2 && $length != 1.5 && !($length == 1.5 && $this->_os === "linux")
        ) {
            trigger_error(
                "Specified stop bit length is invalid",
                E_USER_WARNING
            );

            return false;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec(
                "stty -F " . $this->_device . " " .
                    (($length == 1) ? "-" : "") . "cstopb",
                $out
            );
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec(
                "stty -f " . $this->_device . " " .
                    (($length == 1) ? "-" : "") . "cstopb",
                $out
            );
        } else {
            $ret = $this->_exec(
                "mode " . $this->_winDevice . " STOP=" . $length,
                $out
            );
        }
    }

    /**
     * Configures the flow control
     *
     * @param  string $mode Set the flow control mode. Availible modes :
     *                      -> "none" : no flow control
     *                      -> "rts/cts" : use RTS/CTS handshaking
     *                      -> "xon/xoff" : use XON/XOFF protocol
     * @return bool
     */
    public function confFlowControl($mode)
    {
        $linuxModes = array(
            "none"     => "clocal -crtscts -ixon -ixoff",
            "rts/cts"  => "-clocal crtscts -ixon -ixoff",
            "xon/xoff" => "-clocal -crtscts ixon ixoff"
        );
        $windowsModes = array(
            "none"     => "xon=off octs=off rts=on",
            "rts/cts"  => "xon=off octs=on rts=hs",
            "xon/xoff" => "xon=on octs=off rts=on",
        );

        if ($mode !== "none" and $mode !== "rts/cts" and $mode !== "xon/xoff") {
            trigger_error("Invalid flow control mode specified", E_USER_ERROR);

            return false;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec(
                "stty -F " . $this->_device . " " . $linuxModes[$mode],
                $out
            );
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec(
                "stty -f " . $this->_device . " " . $linuxModes[$mode],
                $out
            );
        } else {
            $ret = $this->_exec(
                "mode " . $this->_winDevice . " " . $windowsModes[$mode],
                $out
            );
        }
    }
}
