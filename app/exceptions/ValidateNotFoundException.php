<?php

namespace app\exceptions;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Throwable;

/**
 * 验证类不存在异常
 */
class ValidateNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    protected string $class;

    public function __construct(string $class, string $message = '', Throwable $previous = null)
    {
        $this->class   = $class;
        $this->message = $message;
        if (empty($message)) {
            $this->message = "validate class not exists: {$class}";
        }

        parent::__construct($this->message, 0, $previous);
    }

    /**
     * 获取类名
     * @access public
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}