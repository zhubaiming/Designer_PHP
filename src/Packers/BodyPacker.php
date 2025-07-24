<?php

declare(strict_types=1);

namespace Hongyi\Designer\Packers;

use Hongyi\Designer\Contracts\PackerInterface;

class BodyPacker implements PackerInterface
{
    public function pack(array $parameters): string
    {
        return empty($parameters) ? '' : json_encode($parameters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function unpack(string $payload): ?array
    {
        $result = json_decode($payload, true);

        return is_array($result) ? $result : null;
    }
}