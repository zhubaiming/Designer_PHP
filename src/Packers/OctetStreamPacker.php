<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * 二进制流(下载文件、上传文件块、RPC 传输)
 */
class OctetStreamPacker implements PackerInterface
{
    public function pack(array $parameters): string|array
    {
        return $parameters['binary'] ?? ''; // 自定义二进制传入格式，如 ['BINARY' => $binaryStream]
    }

    public function unpack(string $payload): string|array|null
    {
        return $payload;
    }

    public function getContentType(): string
    {
        return 'application/octet-stream';
    }
}