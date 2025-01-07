<?php

namespace app\exceptions;

use RuntimeException;

/**
 * 无权操作异常
 */
class UnauthorizedOperationException extends RuntimeException
{
    public function __construct(string $message = 'unauthorized operation', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}