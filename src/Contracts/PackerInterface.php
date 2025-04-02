<?php

declare(strict_types=1);

namespace Hongyi\Designer\Contracts;

/**
 * 打包组件
 * 根据不同的请求情况，对请求内容进行打包和拆包
 */
interface PackerInterface
{
    public function pack(array $parameters): string;

    public function unpack(string $payload): ?array;
}