<?php

namespace Sanchescom\Serial\Contracts;

interface SystemInterface
{
    /**
     * Opens the device for reading and/or writing.
     *
     * @param  string $mode Opening mode : same parameter as fopen()
     *
     * @return bool
     */
    public function open(string $mode = "r+b");

    /**
     * Close the device.
     *
     * @return bool
     */
    public function close();

    /**
     * Send a string to the device.
     *
     * @param string $message      String to be sent to the device
     * @param float  $waitForReply Time to wait for the reply (in seconds)
     */
    public function send(string $message, float $waitForReply = 0.1);

    /**
     * Read the port until no new data are available, then return the content.
     *
     * @param int $count Number of characters to be read (will stop before
     *                   if less characters are in the buffer)
     * @return string
     */
    public function read(int $count = 0);

    /**
     * Flush the output buffer.
     *
     * @return void
     */
    public function flush();
}