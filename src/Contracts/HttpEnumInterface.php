<?php

declare(strict_types=1);

namespace Hongyi\Designer\Contracts;

interface HttpEnumInterface
{
    /**
     * 判断是否为成功的状态码
     *
     * @param int $code
     * @return bool
     */
    public static function isSuccess(int $code): bool;
}