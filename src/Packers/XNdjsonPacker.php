<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

/**
 * Newline Delimited JSON，用于批量传输 JSON(如 Elasticsearch、日志)
 */
class XNdjsonPacker implements PackerInterface
{
    public function pack(array $parameters): string|array
    {
        return implode("\n", array_map('json_encode', $parameters)) . "\n";
    }

    public function unpack(string $payload): string|array|null
    {
        return json_decode($payload, true);
    }

    public function getContentType(): string
    {
        return 'application/x-ndjson';
    }
}