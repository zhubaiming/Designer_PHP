<?php

declare(strict_types=1);

namespace Hongyi\Designer\Plugin;

use Hongyi\Designer\Contract\PluginInterface;
use Hongyi\Designer\Patchwerk;

/**
 * 打包插件
 *
 * 作用: 将「处理」后的 Patchwerk 类下的 $parameters 打包成「有效荷载」
 */
class AddBodyToPayloadPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        // 1、获取处理过后的参数
        // 2、过滤参数中 key 名带有 '_' 的，此类键值为处理过程中所产生的，不需要带到有效荷载中
        $parameters = filter_parameters($patchwerk->getParameters());

        // 3、将过滤后的荷载数组，放置到参数的 _body 字段中
        // 4、通过打包器进行对 _body 的打包，并赋值到荷载中
        $patchwerk->setPayload($parameters);

        $patchwerk->mergeParameters(['_body' => $patchwerk->getPacker()->pack($parameters)]);

        return $next($patchwerk);
    }
}