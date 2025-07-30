<?php

declare(strict_types=1);

namespace Hongyi\Designer;

use Hongyi\Designer\Contracts\HttpEnumInterface;
use Hongyi\Designer\Contracts\PackerInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;

class Patchwerk
{
    /**
     * 请求参数(实际会在插件执行过程中进行修改，产生新的key)
     */
    private array $parameters = [];

    /**
     * 原始输入参数(未做任何修改)
     */
    private array $parametersOrigin = [];

    private ?HttpEnumInterface $httpEnum = null;

    /**
     * 有效荷载(实际存储请求 API 时所需要的所有有效参数)
     */
    private ?string $payload = null;

    /**
     * 打包器，可根据打包类型不同，使用不同的打包器(如: json类型打包器，query类型打包器，xml类型打包器等)
     */
//    private string $packer = PackerInterface::class;
    private ?PackerInterface $packer = null;

    /**
     * 雷达(请求类)
     */
    private ?MessageInterface $radar = null;

    private $direction = null;

    /**
     * 返回结果(实际会在插件执行过程中进行修改，变成新的内容)
     */
    private null|array|MessageInterface $destination = null;

    /**
     * 原始返回结果(未做任何修改)
     */
    private ?MessageInterface $destinationOrigin = null;


    /**
     * 获取当前请求参数
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * 设置当前请求参数
     *
     * @param array $parameters 请求参数
     * @return $this
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * 合并请求参数
     *
     * @param array $parameters 要合并的参数
     * @return $this
     */
    public function mergeParameters(array $parameters): static
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * 移除请求参数
     *
     * @param mixed $key 要移除的键名
     * @return $this
     */
    public function exceptParameters(mixed $key): static
    {
        if (!empty($this->parameters) && array_key_exists($key, $this->parameters) && $key !== 'locale') {
            unset($this->parameters[$key]);
        }

        return $this;
    }

    /**
     * 获取原始请求入参
     *
     * @return array
     */
    public function getParametersOrigin(): array
    {
        return $this->parametersOrigin;
    }

    /**
     * 设置原始请求入参
     *
     * @param array $parametersOrigin 原始请求入参
     * @return $this
     */
    public function setParametersOrigin(array $parametersOrigin): static
    {
        $this->parametersOrigin = $parametersOrigin;

        return $this;
    }

    /**
     * 获取Http响应枚举
     *
     * @return HttpEnumInterface|null
     */
    public function getHttpEnum(): ?HttpEnumInterface
    {
        return $this->httpEnum;
    }

    /**
     * 设置Http响应枚举
     *
     * @param HttpEnumInterface $httpEnum
     * @return $this
     */
    public function setHttpEnum(HttpEnumInterface $httpEnum): static
    {
        $this->httpEnum = $httpEnum;

        return $this;
    }

    /**
     * 获取请求有效荷载
     *
     * @return string|null
     */
    public function getPayload(): string|null
    {
        return $this->payload;
    }

    /**
     * 设置请求有效荷载
     *
     * @param $payload
     * @return $this
     */
    public function setPayload($payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * 获取当前打包器
     *
     * @return PackerInterface|null
     */
//    public function getPacker(): string
//    {
//        return $this->packer;
//    }
    public function getPacker(): ?PackerInterface
    {
        return $this->packer;
    }

    /**
     * 设置当前打包器
     *
     * @param PackerInterface $packer
     * @return $this
     */
//    public function setPacker(string $packer): static
//    {
//        $this->packer = $packer;
//
//        return $this;
//    }
    public function setPacker(PackerInterface $packer): static
    {
        $this->packer = $packer;

        return $this;
    }

    /**
     * 获取当前雷达
     *
     * @return MessageInterface|null
     */
    public function getRadar(): ?RequestInterface
    {
        return $this->radar;
    }

    /**
     * 设置当前雷达
     *
     * @param RequestInterface|null $radar
     * @return $this
     */
    public function setRadar(?RequestInterface $radar): static
    {
        $this->radar = $radar;

        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getDirection(): array|string|null
    {
        return $this->direction;
    }

    /**
     * @param $direction
     * @return $this
     */
    public function setDirection($direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * 获取当前返回结果
     *
     * @return MessageInterface|array|null
     */
    public function getDestination(): MessageInterface|array|null
    {
        return $this->destination;
    }

    /**
     * 设置当前返回结果
     *
     * @param array|MessageInterface|null $destination
     * @return $this
     */
    public function setDestination(null|array|MessageInterface $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * 获取原始返回结果
     *
     * @return MessageInterface|null
     */
    public function getDestinationOrigin(): ?MessageInterface
    {
        return $this->destinationOrigin;
    }

    /**
     * 设置原始返回结果
     *
     * @param MessageInterface|null $destinationOrigin
     * @return $this
     */
    public function setDestinationOrigin(?MessageInterface $destinationOrigin): static
    {
        $this->destinationOrigin = $destinationOrigin;

        return $this;
    }
}