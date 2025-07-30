<?php

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * 直接上传特定类型文件或请求体时文件数据
 */
class ImagePacker implements PackerInterface
{
    public function pack(array $parameters): string
    {
        // TODO: Implement pack() method.
    }

    public function unpack(string $payload): ?array
    {
        // TODO: Implement unpack() method.
    }
}