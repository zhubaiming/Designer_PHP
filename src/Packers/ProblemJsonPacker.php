<?php

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * 错误信息响应结构(RFC 7807 标准)
 */
class ProblemJsonPacker implements PackerInterface
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