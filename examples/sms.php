<?php

include __DIR__ . '/../src/Serial.php';

// Let's start the class
$serial = new \Sanchescom\Serial\Serial();

try {
    // First we must specify the device. This works on both linux and windows (if
    // your linux serial device is /dev/ttyS0 for COM1, etc)
    // If you are using Windows, make sure you disable FIFO from the modem's
    // Device Manager properties pane (Advanced >> Advanced Port Settings...)
    $device = $serial->setDevice('COM4');

    // Then we need to open it
    $device->open('w+');

    // We may need to return if nothing happens for 10 seconds
    $device->setBlockingMode(10);

    // SMS inbox query - mode command and list command
    $device->send("AT", 1);
    var_dump($device->read());
    $device->send("AT+CMGF=1\n\r", 1);
    var_dump($device->read());
    $device->send("AT+CMGL=\"ALL\"\n\r", 2);
    var_dump($device->read());

    // If you want to change the configuration, the device must be closed
    $device->close();
} catch (Exception $e) {

}
