<?php

declare(strict_types=1);

namespace Hongyi\Designer\Plugins;

use Closure;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;

/**
 * 起始插件
 *
 * 作用: 将「输入参数」赋值到「原始输入参数 」
 */
class StartPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, Closure $next): Patchwerk
    {
        $patchwerk->setParametersOrigin($patchwerk->getParameters());

        return $next($patchwerk);
    }
}