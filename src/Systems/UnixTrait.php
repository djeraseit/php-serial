<?php

namespace Sanchescom\Serial\Systems;

trait UnixTrait
{
    protected static $flowControls = [
        "none"     => "clocal -crtscts -ixon -ixoff",
        "rts/cts"  => "-clocal crtscts -ixon -ixoff",
        "xon/xoff" => "-clocal -crtscts ixon ixoff",
    ];
}