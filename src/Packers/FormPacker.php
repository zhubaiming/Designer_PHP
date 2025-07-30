<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * 表单提交，键值对格式，常用于简单表单(如登录)
 */
class FormPacker implements PackerInterface
{
    public function pack(array $parameters): string|array
    {
        return http_build_query($parameters);
    }

    public function unpack(string $payload): string|array|null
    {
        parse_str($payload, $result);
        return $result;
    }

    public function getContentType(): string
    {
        return 'application/x-www-form-urlencoded';
    }
}