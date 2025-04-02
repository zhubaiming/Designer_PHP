<?php

declare(strict_types=1);

namespace Hongyi\Designer;

use GuzzleHttp\Client;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Contracts\ShortcutInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidResponseException;
use Hongyi\Designer\Providers\ConfigServiceProvider;
use Hongyi\Support\Pipeline;

class Vaults
{
    protected static $instance = null;

    private array $providers = [
        ConfigServiceProvider::class
    ];

    private static array $bindings = [];

    public function __construct($config)
    {
        $this->registerProviders($config);
    }

    public static function config(array $config = []): void
    {
        new self($config);
    }

    /**
     * 删除已绑定的容器
     *
     * @param string $alias
     * @return void
     */
    public static function unset(string $alias): void
    {
        unset(self::$bindings[$alias]);
    }

    /**
     * 添加容器绑定
     *
     * @param string $alias
     * @param mixed $service
     * @return void
     */
    public static function set(string $alias, mixed $service): void
    {
        self::$bindings[$alias] = $service;
    }

    /**
     * 获取已绑定的容器
     *
     * @param string $provider
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public static function get(string $provider, array $arguments = [])
    {
        $provider = self::$bindings[$provider];

//        if (is_object($provider)) {
//            return $provider;
//        }
//
//        if (is_string($provider) && class_exists($provider)) {
//            return new $provider();
//        }
//
//        throw new InvalidArgumentException('Invalid provider: must be an object or a valid class name.');

        return is_object($provider) ? $provider : (class_exists($provider) ? new $provider(...$arguments) : throw new Exception("服务[{$provider}]未找到", Exception::CONTAINER_NOT_FOUND));
    }

    /**
     * 注册单个容器
     *
     * @param $provider
     * @param $data
     * @return void
     */
    public static function registerProvider($provider, $data = null): void
    {
        $var = new $provider();

        self::set($provider, $var->register($data));
    }

    /**
     * 实现制定快捷方式的全部插件
     *
     * @param string $shortcut
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    public static function shortcut(string $shortcut, array $parameters): mixed
    {
        /*
         * class_exists 类是否存在
         * class_implements 返回指定类或对象所实现的所有接口列表
         */
        if (!class_exists($shortcut) || !in_array(ShortcutInterface::class, class_implements($shortcut))) {
            throw new Exception("参数异常: [{$shortcut}] 未实现 `ShortcutInterface`", Exception::INTERFACE_ERROR);
        }

        return self::handle($shortcut::getPlugins(), $parameters, $shortcut::SEND_HTTP ?? true);
    }

    /**
     * 插件组的管道流水线的具体设定和执行
     *
     * @param array $plugins
     * @param array $parameters
     * @param bool $sendHttp
     * @return mixed
     * @throws Exception
     */
    public static function handle(array $plugins, array $parameters, bool $sendHttp = true)
    {
        self::verifyPlugin($plugins);

//        $pipeline = self::make(Pipeline::class);
        $pipeline = new Pipeline();

        // 构建基础管道
        $patchwerkPipeline = $pipeline
            ->send((new Patchwerk())->setParameters($parameters))
            ->through($plugins)
            ->via('handle');

        // 根据条件决定后续操作
        $patchwerk = $sendHttp
            ? $patchwerkPipeline->then(static fn($patchwerk) => self::ignite($patchwerk))
            : $patchwerkPipeline->thenReturn();

        return !empty($parameters['_return_object']) ? $patchwerk : $patchwerk->getDestination();
    }

    /**
     * 发起网络请求
     *
     * @param Patchwerk $patchwerk
     * @return Patchwerk
     * @throws InvalidResponseException
     */
    public static function ignite(Patchwerk $patchwerk): Patchwerk
    {
//        if (!shoud_do_http_request($patchwerk->getDirection())){
//            return $patchwerk;
//        }

        $http = new Client();

        try {
            $response = $http->sendRequest($patchwerk->getRadar());

            $patchwerk->setDestination(clone $response)
                ->setDestinationOrigin(clone $response);
        } catch (\Throwable $e) {
            throw new InvalidResponseException('响应异常: 请求第三方 API 出错 - ' . $e->getMessage(), Exception::RESPONSE_REQUEST_ERROR);
        }

        return $patchwerk;
    }

    /**
     * 验证插件
     *
     * @param array $plugins
     * @return void
     * @throws Exception
     */
    protected static function verifyPlugin(array $plugins): void
    {
        foreach ($plugins as $plugin) {
            if (is_callable($plugin)) continue;

            if ((is_object($plugin) || (is_string($plugin) && class_exists($plugin))) && in_array(PluginInterface::class, class_implements($plugin))) continue;

            throw new Exception("参数异常: [{$plugin}] 插件未实现 `PluginInterface`", Exception::INTERFACE_ERROR);
        }
    }

    /**
     * 注册当前已定义的全部容器
     *
     * @param $config
     * @return void
     */
    private function registerProviders($config): void
    {
        foreach ($this->providers as $provider) {
            self::registerProvider($provider, $config);
        }
    }
}