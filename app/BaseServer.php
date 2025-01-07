<?php

namespace app;

class BaseServer
{
    /**
     * 实例集合
     *
     * @var array
     */
    protected static array $instances = [];

    /**
     * 获取实例
     *
     * @param bool $newInstance
     * @return static
     */
    public static function getInstance(bool $newInstance = false): static
    {
        if ($newInstance) {
            return new static();
        }

        $key = static::class;
        if (empty(self::$instances[$key])) {
            self::$instances[$key] = new static();
        }

        return self::$instances[$key];
    }
}