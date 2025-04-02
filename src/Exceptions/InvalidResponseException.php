<?php

declare(strict_types=1);

namespace Hongyi\Designer\Exceptions;

class InvalidResponseException extends Exception
{
    public function __construct(string $message = '响应异常', int $code = self::RESPONSE_ERROR, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}