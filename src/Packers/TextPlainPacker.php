<?php

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * 纯文本数(极少用于接口请求)
 */
class TextPlainPacker implements PackerInterface
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