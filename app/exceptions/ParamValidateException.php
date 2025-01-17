<?php

namespace app\exceptions;

use RuntimeException;

/**
 * 参数验证异常
 */
class ParamValidateException extends RuntimeException
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}