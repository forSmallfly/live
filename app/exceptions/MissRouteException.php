<?php

namespace app\exceptions;

use RuntimeException;

/**
 * miss路由异常
 */
class MissRouteException extends RuntimeException
{
    public function __construct(string $message = 'not found', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}