<?php

namespace app\exceptions;

use RuntimeException;

/**
 * 空控制器异常
 */
class EmptyControllerException extends RuntimeException
{
    public function __construct(string $message = "requested resource doesn't exist", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}