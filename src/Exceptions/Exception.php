<?php

declare(strict_types=1);

namespace Hongyi\Designer\Exceptions;

class Exception extends \Exception
{
    public const UNKNOWN_ERROR = 9999;
    public const INTERFACE_ERROR = 9998;

    /**
     * 容器
     */
    public const CONTAINER_ERROR = 9100;
    public const CONTAINER_NOT_FOUND = 9101;

    /**
     * 配置
     */
    public const CONFIG_ERROR = 9200;
    public const CONFIG_FILE_ERROR = 9201;
//    public const CONFIG_

    /**
     * 参数
     */
    public const PARAMS_ERROR = 9300;
//    public const PARAMS_
//    public const PARAMS_
//    public const PARAMS_
//    public const PARAMS_

    /**
     * 响应
     */
    public const RESPONSE_ERROR = 9400;
    public const RESPONSE_REQUEST_ERROR = 9401;

//    public const RESPONSE_

    /**
     * 网络请求配置
     */
    public const HTTP_REQUEST_ERROR = 9500;

    public function __construct(string $message = '未知异常', int $code = self::UNKNOWN_ERROR, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}