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
        $headers = [];

        if (isset($parameters['_headers']['Content-Type'])) $headers['Content-Type'] = $parameters['_headers']['Content-Type'];
        if (isset($parameters['_headers']['User-Agent'])) $headers['User-Agent'] = $parameters['_headers']['User-Agent'];

        if (isset($parameters['_authorization'])) {
            $headers['Authorization'] = $parameters['_authorization'];
        }

        return $headers;
    }
}