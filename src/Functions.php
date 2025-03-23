<?php

declare(strict_types=1);

if (!function_exists('filter_parameters')) {
    function filter_parameters(array $parameters, \Closure $closure = null): array
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
        return isset($parameters['_url']) ? strtoupper($parameters['_url']) : null;
    }
}

if (!function_exists('get_radar_body')) {
    function get_radar_body(array $parameters): ?string
    {
        return isset($parameters['_body']) ? strtoupper($parameters['_url']) : null;
    }
}