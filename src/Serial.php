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
     * Device set function : used to set the device name/address.
     * -> linux : use the device address, like /dev/ttyS0
     * -> osx : use the device address, like /dev/tty.serial
     * -> windows : use the COMxx device name, like COM1 (can also be used
     *     with linux)
     *
     * @param  string $device the name of the device to be used
     * @return bool
     */
    public function deviceSet($device)
    {
        if ($this->_dState !== SERIAL_DEVICE_OPENED) {
            if ($this->_os === "linux") {
                if (preg_match("@^COM(\\d+):?$@i", $device, $matches)) {
                    $device = "/dev/ttyS" . ($matches[1] - 1);
                }

                if ($this->_exec("stty -F " . $device) === 0) {
                    $this->_device = $device;
                    $this->_dState = SERIAL_DEVICE_SET;

                    return true;
                }
            } elseif ($this->_os === "osx") {
                if ($this->_exec("stty -f " . $device) === 0) {
                    $this->_device = $device;
                    $this->_dState = SERIAL_DEVICE_SET;

                    return true;
                }
            } elseif ($this->_os === "windows") {
                if (preg_match("@^COM(\\d+):?$@i", $device, $matches)
                        and $this->_exec(
                            exec("mode " . $device . " xon=on BAUD=9600")
                        ) === 0
                ) {
                    $this->_winDevice = "COM" . $matches[1];
                    $this->_device = "\\.com" . $matches[1];
                    $this->_dState = SERIAL_DEVICE_SET;

                    return true;
                }
            }

            trigger_error("Specified serial port is not valid", E_USER_WARNING);

            return false;
        } else {
            trigger_error("You must close your device before to set an other " .
                          "one", E_USER_WARNING);

            return false;
        }
    }

    /**
     * Configure the Baud Rate
     * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
     * 57600 and 115200.
     *
     * @param  int  $rate the rate to set the port in
     * @return bool
     */
    public function confBaudRate($rate)
    {
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set the baud rate : the device is " .
                          "either not set or opened", E_USER_WARNING);

            return false;
        }

        $validBauds = array (
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
            115200 => 115200
        );

        if (isset($validBauds[$rate])) {
            if ($this->_os === "linux") {
                $ret = $this->_exec(
                    "stty -F " . $this->_device . " " . (int) $rate,
                    $out
                );
            } elseif ($this->_os === "osx") {
                $ret = $this->_exec(
                    "stty -f " . $this->_device . " " . (int) $rate,
                    $out
                );
            } elseif ($this->_os === "windows") {
                $ret = $this->_exec(
                    "mode " . $this->_winDevice . " BAUD=" . $validBauds[$rate],
                    $out
                );
            } else {
                return false;
            }

            if ($ret !== 0) {
                trigger_error(
                    "Unable to set baud rate: " . $out[1],
                    E_USER_WARNING
                );

                return false;
            }

            return true;
        } else {
            return false;
        }
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
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error(
                "Unable to set parity : the device is either not set or opened",
                E_USER_WARNING
            );

            return false;
        }

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

        if ($ret === 0) {
            return true;
        }

        trigger_error("Unable to set parity : " . $out[1], E_USER_WARNING);

        return false;
    }

    /**
     * Sets the length of a character.
     *
     * @param  int  $int length of a character (5 <= length <= 8)
     * @return bool
     */
    public function confCharacterLength($int)
    {
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set length of a character : the device " .
                          "is either not set or opened", E_USER_WARNING);

            return false;
        }

        $int = (int) $int;
        if ($int < 5) {
            $int = 5;
        } elseif ($int > 8) {
            $int = 8;
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec(
                "stty -F " . $this->_device . " cs" . $int,
                $out
            );
        } elseif ($this->_os === "osx") {
            $ret = $this->_exec(
                "stty -f " . $this->_device . " cs" . $int,
                $out
            );
        } else {
            $ret = $this->_exec(
                "mode " . $this->_winDevice . " DATA=" . $int,
                $out
            );
        }

        if ($ret === 0) {
            return true;
        }

        trigger_error(
            "Unable to set character length : " .$out[1],
            E_USER_WARNING
        );

        return false;
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
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set the length of a stop bit : the " .
                          "device is either not set or opened", E_USER_WARNING);

            return false;
        }

        if ($length != 1
                and $length != 2
                and $length != 1.5
                and !($length == 1.5 and $this->_os === "linux")
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

        if ($ret === 0) {
            return true;
        }

        trigger_error(
            "Unable to set stop bit length : " . $out[1],
            E_USER_WARNING
        );

        return false;
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
        if ($this->_dState !== SERIAL_DEVICE_SET) {
            trigger_error("Unable to set flow control mode : the device is " .
                          "either not set or opened", E_USER_WARNING);

            return false;
        }

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

        if ($ret === 0) {
            return true;
        } else {
            trigger_error(
                "Unable to set flow control : " . $out[1],
                E_USER_ERROR
            );

            return false;
        }
    }
}
