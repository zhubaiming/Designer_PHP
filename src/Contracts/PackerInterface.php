<?php

declare(strict_types=1);

namespace Hongyi\Designer\Contracts;

use GuzzleHttp\Psr7\MultipartStream;

/**
 * 打包组件
 * 根据不同的请求情况，对请求内容进行打包和拆包
 */
interface PackerInterface
{
    public function pack(array $parameters): string|array|MultipartStream;

    public function unpack(string $payload): string|array|null;

    public function getContentType(): string;
}