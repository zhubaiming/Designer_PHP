<?php

declare(strict_types=1);

namespace Hongyi\Designer\Exceptions;

class InvalidConfigException extends Exception
{
    public function __construct(string $message = '配置异常', int $code = self::CONFIG_ERROR, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}