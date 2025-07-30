<?php

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * JSON API 规范所用的格式
 */
class VndApiJsonPacker implements PackerInterface
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