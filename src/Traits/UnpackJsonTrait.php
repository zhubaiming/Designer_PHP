<?php

namespace Hongyi\Designer\Traits;

trait UnpackJsonTrait
{
    public function unpack(string $payload): string|array|null
    {
        $result = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

        }

        return $result;
    }
}