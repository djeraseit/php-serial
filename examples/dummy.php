<?php

include __DIR__ . '/../src/Serial.php';

// Let's start the class
$serial = new \Sanchescom\Serial\Serial();

try {
    // First we must specify the device. This works on both linux and windows (if
    // your linux serial device is /dev/ttyS0 for COM1, etc)
    $device = $serial->setDevice('COM5');

    // We can change the baud rate, parity, length, stop bits, flow control
    $device->setBaudRate(2400);
    $device->setParity("none");
    $device->setCharacterLength(8);
    $device->setStopBits(1);
    $device->setFlowControl("none");

    // Then we need to open it
    $device->open();

    // To write into
    $device->send('Hello!');

    // Or to read from
    $read = $device->read();

    // If you want to change the configuration, the device must be closed
    $device->close();

    // We can change the baud rate
    $device->setBaudRate(2400);
} catch (Exception $e) {

}

// etc...
//
//
/* Notes from Jim :
> Also, one last thing that would be good to document, maybe in example.php:
>  The actual device to be opened caused me a lot of confusion, I was
> attempting to open a tty.* device on my system and was having no luck at
> all, until I found that I should actually be opening a cu.* device instead!
>  The following link was very helpful in figuring this out, my USB/Serial
> adapter (as most probably do) lacked DTR, so trying to use the tty.* device
> just caused the code to hang and never return, it took a lot of googling to
> realize what was going wrong and how to fix it.
>
> http://lists.apple.com/archives/darwin-dev/2009/Nov/msg00099.html

Riz comment : I've definately had a device that didn't work well when using cu., but worked fine with tty. Either way, a good thing to note and keep for reference when debugging.
 */
