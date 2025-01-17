<?php

namespace app\exceptions;

use RuntimeException;

/**
 * 未授权异常
 */
class UnauthorizedException extends RuntimeException
{
    public function __construct(string $message = 'unauthorized', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}