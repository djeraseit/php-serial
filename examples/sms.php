<?php

include __DIR__ . '/../src/Serial.php';

// Let's start the class
$serial = new \Sanchescom\Serial\Serial();

// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
// If you are using Windows, make sure you disable FIFO from the modem's
// Device Manager properties pane (Advanced >> Advanced Port Settings...)

$serial->deviceSet("COM4");

// Then we need to open it
$serial->deviceOpen('w+');

// We may need to return if nothing happens for 10 seconds
stream_set_timeout($serial->_dHandle, 10);

// We can change the baud rate
$serial->confBaudRate(9600);

// SMS inbox query - mode command and list command
$serial->sendMessage("AT",1);
var_dump($serial->readPort());
$serial->sendMessage("AT+CMGF=1\n\r",1);
var_dump($serial->readPort());
$serial->sendMessage("AT+CMGL=\"ALL\"\n\r",2);
var_dump($serial->readPort());

// If you want to change the configuration, the device must be closed
$serial->deviceClose();

try {
    // First we must specify the device. This works on both linux and windows (if
    // your linux serial device is /dev/ttyS0 for COM1, etc)
    // If you are using Windows, make sure you disable FIFO from the modem's
    // Device Manager properties pane (Advanced >> Advanced Port Settings...)
    $device = $serial->setDevice('COM4');

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
