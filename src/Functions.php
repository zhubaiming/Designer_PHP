<?php

declare(strict_types=1);

use Hongyi\Designer\Providers\ConfigServiceProvider;
use Hongyi\Designer\Vaults;
use Random\RandomException;

if (!function_exists('filter_parameters')) {
    function filter_parameters(array $parameters, ?Closure $closure = null): array
    {
        return array_filter($parameters, fn($v, $k) => !str_starts_with($k, '_') && !is_null($v) && (is_null($closure) || $closure($k, $v)), ARRAY_FILTER_USE_BOTH);
    }
}

if (!function_exists('get_radar_method')) {
    function get_radar_method(array $parameters): ?string
    {
        return isset($parameters['_method']) ? strtoupper($parameters['_method']) : null;
    }
}

if (!function_exists('get_radar_url')) {
    function get_radar_url(array $parameters): ?string
    {
        return $parameters['_url'] ?? null;
    }
}

if (!function_exists('get_radar_body')) {
    function get_radar_body(array $parameters): ?string
    {
        return isset($parameters['_body']) ? strtoupper($parameters['_url']) : null;
    }
}

if (!function_exists('random_nonce')) {
    /**
     * @throws RandomException
     */
    function random_nonce(int $length): string
    {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }
}

if (!function_exists('get_config')) {
    /**
     * @throws \Hongyi\Designer\Exceptions\Exception
     */
    function get_config(string $channel, string $type = 'default', array $params = [])
    {
        $config = Vaults::get(ConfigServiceProvider::class)->get($channel);

        return $config[$type] ?? $params;
    }
}

if (!function_exists('get_parent_namespace')) {
    function get_parent_namespace(string $namespace, int $levels = 1): string
    {
        while ($levels-- > 0 && ($pos = strrpos($namespace, '\\')) !== false) {
            $namespace = substr($namespace, 0, $pos);
        }

        return $namespace;
    }
}