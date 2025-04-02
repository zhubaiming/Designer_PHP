<?php

declare(strict_types=1);

namespace Hongyi\Designer\Plugins;

use GuzzleHttp\Psr7\Request;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

use function get_radar_method;
use function get_radar_url;

/**
 * 装载雷达插件
 *
 * 作用: 向 Patchwerk 中设置雷达(即: 请求类)，以及雷达的各项请求属性，以方便后续直接调用请求
 */
class AddRadarPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();

        $patchwerk->setRadar(new Request(
            get_radar_method($parameters),
            get_radar_url($parameters),
            $this->mergeHeaders($parameters),
            $patchwerk->getPayload()
        ));

        return $next($patchwerk);
    }

    protected function mergeHeaders($parameters): array
    {
        $userAgent = 'Third-Party Processor';
        $headers = [
            'Accept' => 'application/json, text/plain, application/x-gzip',
            'Content-Type' => 'application/json; charset=utf-8',
            'User-Agent' => $userAgent
        ];

        if (!empty($parameters['_headers'])) {
            $headers = array_merge($headers, $parameters['_headers']);
            $headers['User-Agent'] = $headers['User-Agent'] !== $userAgent ? $userAgent . '(' . $headers['User-Agent'] . ')' : $headers['User-Agent'];
        }

        if (isset($parameters['_authorization'])) {
            $headers['Authorization'] = $parameters['_authorization'];
        }

        return $headers;
    }
}