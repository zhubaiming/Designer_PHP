<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use GuzzleHttp\Psr7\MultipartStream;
use Hongyi\Designer\Contracts\PackerInterface;
use Hongyi\Designer\Traits\UnpackJsonTrait;

/**
 * 表单提交，支持文件上传，字段为多个分块
 */
class FormDataPacker implements PackerInterface
{
    use UnpackJsonTrait;

    public function pack(array $parameters): string|array|MultipartStream
    {
        $elements = array_map(fn($k) => is_array($parameters[$k])
            ? ['name' => $k, 'contents' => file_get_contents($parameters[$k]['tmp_name']), 'filename' => $parameters[$k]['name'], 'headers' => ['Content-Type' => $parameters[$k]['type']]]
            : ['name' => $k, 'contents' => $parameters[$k]],
            array_keys($parameters));

        return new MultipartStream($elements);
    }

    public function getContentType(): string
    {
        return 'multipart/form-data';
    }
}