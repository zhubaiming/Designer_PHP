<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use GuzzleHttp\Psr7\MultipartStream;
use Hongyi\Designer\Contracts\PackerInterface;

/**
 * JSON 格式数据提交，现代 Web API 常用
 */
class JsonPacker implements PackerInterface
{
    public function pack(array $parameters): string|array|MultipartStream
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

    /*
     *  public function pack(array $parameters): string
    {
        return empty($parameters) ? '' : json_encode($parameters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function unpack(string $payload): ?array
    {
        $result = json_decode($payload, true);

        return is_array($result) ? $result : null;
    }
     */
}