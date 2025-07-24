<?php

declare(strict_types=1);

namespace Hongyi\Designer\Plugins;

use Closure;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

use function filter_parameters;

/**
 * 打包插件
 *
 * 作用: 将「处理」后的 Patchwerk 类下的 $parameters 打包成「有效荷载」
 */
class AddBodyToPayloadPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, Closure $next): Patchwerk
    {
        // 1、获取处理过后的参数
        // 2、过滤参数中 key 名带有 '_' 的，此类键值为处理过程中所产生的，不需要带到有效荷载中
        // 3、生成过滤后的有效荷载
        $body = $patchwerk->getPacker()->pack(filter_parameters($patchwerk->getParameters()));

        // 4、通过打包器进行对 _body 的打包，并赋值到荷载中
        $patchwerk->setPayload($body);
        // 5、将有效荷载，放置到参数的 _body 字段中
        $patchwerk->mergeParameters(['_body' => $body]);

        return $next($patchwerk);
    }
}