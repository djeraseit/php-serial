<?php

namespace Sanchescom\Serial\Systems;

/**
 * Trait UnixTrait.
 */
trait UnixTrait
{
    /** @var array */
    protected static $flowControls = [
        "none" => "clocal -crtscts -ixon -ixoff",
        "rts/cts" => "-clocal crtscts -ixon -ixoff",
        "xon/xoff" => "-clocal -crtscts ixon ixoff",
    ];

    /** @var string */
    protected static $utility = "stty";
}
