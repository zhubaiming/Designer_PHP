<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * JSON 格式数据提交，现代 Web API 常用
 */
class JsonPacker implements PackerInterface
{
    public function pack(array $parameters): string|array
    {
        return json_encode($parameters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function unpack(string $payload): string|array|null
    {
        return json_decode($payload, true);
    }

    public function getContentType(): string
    {
        return 'application/json; charset=utf-8;';
    }
}