<?php

namespace app\exceptions;

use RuntimeException;

/**
 * 验证场景未定义异常
 */
class ValidateSceneUndefinedException extends RuntimeException
{
    public function __construct(string $class, string $scene, string $message = '')
    {
        $this->message = $message;
        if (empty($message)) {
            $this->message = "validate scene undefined: {$class}@{$scene}";
        }

        parent::__construct($this->message);
    }
}