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
}
