<?php

declare(strict_types=1);

namespace Hongyi\Designer\Plugins;

use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidResponseException;
use Hongyi\Designer\Patchwerk;

/**
 * 解析器插件
 *
 * 作用: 根据返回结果及返回格式，进行结果内容解析，并判断返回状态是否在规定的成功状态中
 *
 * 流程: 先向下执行，等待请求返回后，处理返回内容
 */
class ParserPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        $this->validateResponse($patchwerk);

        $patchwerk->setDestination($patchwerk->getPacker()->unpack($patchwerk->getDestination()->getBody()->getContents()));

        // 流重置，可以让后续继续读取
        $patchwerk->getDestinationOrigin()->getBody()->seek(0);

        return $patchwerk;
    }

    protected function validateResponse(Patchwerk $patchwerk): void
    {
        if (!$patchwerk->getHttpEnum()::isSuccess($patchwerk->getDestinationOrigin()->getStatusCode())) {
            throw new InvalidResponseException('微信返回状态码异常，请检查参数是否错误', Exception::RESPONSE_ERROR);
        }
    }
}