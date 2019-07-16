<?php

namespace Sanchescom\Serial\Contracts;

/**
 * Interface ConfigInterface.
 */
interface ConfigInterface
{
    /**
     * Configure the Baud Rate
     * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
     * 57600 and 115200.
     *
     * @param  int $rate the rate to set the port in
     * @return bool
     */
    public function setBaudRate(int $rate);

    /**
     * Configure parity.
     * Modes : odd, even, none
     *
     * @param  string $parity one of the modes
     *
     * @return void
     */
    public function setParity($parity);

    /**
     * Sets the length of a character.
     *
     * @param  int $length length of a character (5 <= length <= 8)
     *
     * @return void
     */
    public function setCharacterLength(int $length);

    /**
     * Sets the length of stop bits.
     *
     * @param  float $length the length of a stop bit. It must be either 1,
     *                       1.5 or 2. 1.5 is not supported under linux and on
     *                       some computers.
     * @return void
     */
    public function setStopBits($length);

    /**
     * Configures the flow control
     *
     * @param  string $mode Set the flow control mode. Availible modes :
     *                      -> "none" : no flow control
     *                      -> "rts/cts" : use RTS/CTS handshaking
     *                      -> "xon/xoff" : use XON/XOFF protocol
     * @return void
     */
    public function setFlowControl($mode);
}
