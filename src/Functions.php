<?php

declare(strict_types=1);

use Hongyi\Designer\Providers\ConfigServiceProvider;
use Hongyi\Designer\Vaults;
use Random\RandomException;
use Hongyi\Designer\Direction\NoHttpRequestDirection;

if (!function_exists('filter_parameters')) {
    /**
     * 过滤请求参数
     * 当请求参数的键是以_开头时，视为插件轮转过程中用到的参数，而非实际请求时发送的参数，在拼接成实际请求参数时，进行过滤
     *
     * @param array $parameters
     * @param Closure|null $closure
     * @return array
     */
    function filter_parameters(array $parameters, ?Closure $closure = null): array
    {
        return array_filter($parameters, fn($v, $k) => !str_starts_with($k, '_') && !is_null($v) && (is_null($closure) || $closure($k, $v)), ARRAY_FILTER_USE_BOTH);
    }
}

if (!function_exists('get_radar_method')) {
    /**
     * 获取实际请求类型
     *
     * @param array $parameters
     * @return string|null
     */
    function get_radar_method(array $parameters): ?string
    {
        return isset($parameters['_method']) ? strtoupper($parameters['_method']) : null;
    }
}

if (!function_exists('get_radar_url')) {
    /**
     * 获取实际请求的网络地址
     *
     * @param array $parameters
     * @return string|null
     */
    function get_radar_url(array $parameters): ?string
    {
        return $parameters['_url'] ?? null;
    }
}

if (!function_exists('get_radar_body')) {
    /**
     * 获取实际请求参数体
     *
     * @param array $parameters
     * @return string|null
     */
    function get_radar_body(array $parameters): ?string
    {
        return isset($parameters['_body']) ? strtoupper($parameters['_url']) : null;
    }
}

if (!function_exists('random_nonce')) {
    /**
     * 根据长度生成随机字符串
     *
     * @param int $length 随机字符串长度
     * @param bool $upperCase 随机字符串中的字母是否大写
     * @return string
     * @throws RandomException
     */
    function random_nonce(int $length = 32, bool $upperCase = true): string
    {
        $str = substr(bin2hex(random_bytes($length)), 0, $length);
        return $upperCase ? strtoupper($str) : $str;
    }
}

if (!function_exists('get_config')) {
    /**
     * 根据渠道和类型参数，获取预置的配置项
     *
     * @param string $channel 渠道参数
     * @param string $type 类型参数
     * @param array $params 未获取到时的默认值
     * @return array|mixed
     * @throws \Hongyi\Designer\Exceptions\Exception
     */
    function get_config(string $channel, string $type = 'default', array $params = [])
    {
        $config = Vaults::get(ConfigServiceProvider::class)->get($channel);

        return $config[$type] ?? $params;
    }
}

if (!function_exists('get_parent_namespace')) {
    /**
     * 获取父空间命名
     *
     * @param string $namespace
     * @param int $levels
     * @return string
     */
    function get_parent_namespace(string $namespace, int $levels = 1): string
    {
        while ($levels-- > 0 && ($pos = strrpos($namespace, '\\')) !== false) {
            $namespace = substr($namespace, 0, $pos);
        }

        return $namespace;
    }
}

if (!function_exists('should_do_http_request')) {
    /**
     * 是否发起网络请求
     * 对于通知、回调类的方法，不需要发起网络请求，而是在正常解密数据后，直接返回给调用方
     *
     * @param string|bool $direction
     * @return bool
     */
    function should_do_http_request(string|bool|null $direction = true): bool
    {
        return is_null($direction) || (
            is_bool($direction)
                ? $direction
                : (NoHttpRequestDirection::class !== $direction && !in_array(NoHttpRequestDirection::class, class_parents($direction)))
            );
    }
}

if (!function_exists('array_merge_recursive_distinct')) {
    function array_merge_recursive_distinct(array $base, array $override, array $options = []): array
    {
        $defaults = [
            'overwrite_scalars' => true,    // 是否覆盖标量值
            'merge_arrays' => true,    // 是否合并数组(数值数组)
            'deduplicate' => true,    // 是否去重(仅针对数值数组)
            'preserve_keys' => false    //是否保留数值数组的键(true: 不会变成连续索引)
        ];

        $opts = array_merge($defaults, $options);

        foreach ($override as $key => $value) {
            if (array_key_exists($key, $base)) {
                // 都是数组
                if (is_array($base[$key]) && is_array($value)) {
                    // 区分是否时关联数组还是索引数组
                    if (array_is_list($base[$key]) && array_is_list($value)) {
                        // 数值数组
                        if ($opts['merge_arrays']) {
                            $merged = $opts['preserve_keys'] ? $base[$key] + $value : array_merge($base[$key], $value);

                            $base[$key] = $opts['deduplicate'] ? array_values(array_unique($merged, SORT_REGULAR)) : $merged;
                        } else {
                            // 覆盖
                            $base[$key] = $value;
                        }
                    } else {
                        // 关联数组: 递归合并
                        $base[$key] = array_merge_recursive_distinct($base[$key], $value, $opts);
                    }
                } else {
                    // 一个时标量或不同类型
                    if ($opts['overwrite_scalars']) $base[$key] = $value;

                    // 否则保留 $base 中原始值
                }
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }
}