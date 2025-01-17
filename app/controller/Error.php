<?php

namespace app\controller;

use app\exceptions\EmptyControllerException;
use app\exceptions\MissRouteException;

class Error
{
    /**
     * 空控制器
     *
     * @param $method
     * @param $args
     * @return string
     */
    public function __call($method, $args)
    {
        throw new EmptyControllerException();
    }

    /**
     * miss路由
     *
     * @return string
     */
    public function miss(): string
    {
        throw new MissRouteException();
    }
}