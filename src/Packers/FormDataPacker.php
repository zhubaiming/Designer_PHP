<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * 表单提交，支持文件上传，字段为多个分块
 */
class FormDataPacker implements PackerInterface
{
    public function pack(array $parameters): string|array
    {
        return array_map(fn($k) => ['name' => $k, 'contents' => $parameters[$k]], array_keys($parameters));
    }

    public function unpack(string $payload): string|array|null
    {
        return $payload;
    }

    public function getContentType(): string
    {
        return 'multipart/form-data';
    }
}