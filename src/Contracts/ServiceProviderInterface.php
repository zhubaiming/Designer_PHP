<?php

declare(strict_types=1);

namespace Hongyi\Designer\Contracts;

/**
 * 服务提供者
 * 定义每个服务提供者需要提供的具体服务执行类
 */
interface ServiceProviderInterface
{
    public function register(mixed $data = null);
}