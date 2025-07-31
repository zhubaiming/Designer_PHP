<?php

declare(strict_types=1);

namespace Hongyi\Designer\Plugins;

use Hongyi\Designer\Contracts\HttpEnumInterface;
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
    /**
     * @throws InvalidResponseException
     */
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $patchwerk = $next($patchwerk);

        $response = $patchwerk->getDestination();

        if (!is_null($httpEnum = $patchwerk->getHttpEnum())) {
            $this->validateResponse($httpEnum, $response->getStatusCode());
        }

        $patchwerk->setDestination([
            'code' => $response->getStatusCode(),
            'headers' => array_map(fn($v) => (is_array($v) && count($v) === 1) ? reset($v) : $v, $response->getHeaders()),
            'body' => $patchwerk->getPacker()->unpack($response->getBody()->getContents())
        ]);

        // 流重置，可以让后续继续读取
        if ($response->getBody()->isSeekable()) $response->getBody()->rewind();

        return $patchwerk;
    }

    /**
     * @throws InvalidResponseException
     */
    protected function validateResponse(HttpEnumInterface $httpEnum, int $statusCode): void
    {
        if (!$httpEnum::isSuccess($statusCode)) {
            throw new InvalidResponseException('网络请求返回状态码异常，请检查参数是否错误', Exception::RESPONSE_ERROR);
        }
    }
}