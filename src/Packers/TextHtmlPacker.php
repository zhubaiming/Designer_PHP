<?php

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * 页面 HTML 数据，浏览器发起的表单提交可能使用
 */
class TextHtmlPacker implements PackerInterface
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