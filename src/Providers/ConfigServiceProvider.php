<?php

declare(strict_types=1);

namespace Hongyi\Designer\Providers;

use Hongyi\Designer\Contracts\ServiceProviderInterface;
use Hongyi\Designer\Services\Config;
use Hongyi\Designer\Vaults;

class ConfigServiceProvider implements ServiceProviderInterface
{
    private array $config = [];

    public function register(mixed $data = null): void
    {
        Vaults::set(self::class, new Config(array_replace_recursive($this->config, $data ?? [])));
    }
}