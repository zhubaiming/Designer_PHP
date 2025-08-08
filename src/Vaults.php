<?php

declare(strict_types=1);

namespace Hongyi\Designer;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Contracts\ShortcutInterface;
use Hongyi\Designer\Exceptions\Exception;
use Hongyi\Designer\Exceptions\InvalidHttpException;
use Hongyi\Designer\Exceptions\InvalidResponseException;
use Hongyi\Designer\Providers\ConfigServiceProvider;
use Hongyi\Support\Pipeline;
use Psr\Http\Message\RequestInterface;
use Throwable;

use function should_do_http_request;

class Vaults
{

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
    public static function get(string $provider, array $arguments = []): mixed
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

        return is_object($provider) ? $provider : (class_exists($provider) ? new $provider(...$arguments) : throw new Exception("服务[$provider]未找到", Exception::CONTAINER_NOT_FOUND));
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
        new $provider()->register($data);
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
            throw new Exception("参数异常: [$shortcut] 未实现 `ShortcutInterface`", Exception::INTERFACE_ERROR);
        }

        return self::handle($shortcut::getPlugins(), $parameters, property_exists($shortcut, 'sendHttp') ? $shortcut::$sendHttp : true);
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
    public static function handle(array $plugins, array $parameters, bool $sendHttp = true): mixed
    {
        self::verifyPlugin($plugins);

//        $pipeline = self::make(Pipeline::class);
        $pipeline = new Pipeline();

        // 构建基础管道
        $patchwerkPipeline = $pipeline
            ->send(new Patchwerk()->setParameters($parameters))
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
     * @throws InvalidHttpException
     */
    public static function ignite(Patchwerk $patchwerk): Patchwerk
    {
        $real_http_send = should_do_http_request($patchwerk->getDirection());

        $request = $patchwerk->getRadar();

        if (is_null($request)) throw new InvalidHttpException('请求异常: 没有正确设置 request', Exception::HTTP_REQUEST_ERROR);

        $http = self::buildHttp($real_http_send, $request);

        /*
         * 请求选项
         * 具体参考: https://guzzle-zh-cn.readthedocs.io/zh-cn/latest/request-options.html
         */
        $clientOptions = [
            RequestOptions::ALLOW_REDIRECTS => false, // 是否允许重定向
            RequestOptions::AUTH => null, // 请求是否进行认证
            RequestOptions::CONNECT_TIMEOUT => 0, // 等待服务器响应的最大时间，0为不限时
            RequestOptions::DEBUG => false, // 是否开启调试输出
            RequestOptions::DECODE_CONTENT => true, // 是否自动解码 Content-Encoding 响应
            RequestOptions::DELAY => 0, // 发送请求之前延迟的毫秒数
            RequestOptions::EXPECT => true,
            RequestOptions::FORCE_IP_RESOLVE => 'v4', // 希望 HTTP 处理器仅使用的协议v4 - ipv4, v6 - ipv6
            RequestOptions::HTTP_ERRORS => false, // 遵循PSR-18
            RequestOptions::SYNCHRONOUS => true,
            RequestOptions::TIMEOUT => 0,
            RequestOptions::VERSION => '1.1',
        ];

        try {
//            $response = $http->sendRequest($request);
            $response = $http->send($request, $clientOptions);

            $patchwerk->setDestination(clone $response)
                ->setDestinationOrigin(clone $response);
        } catch (Throwable $e) {
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

            throw new Exception("参数异常: [$plugin] 插件未实现 `PluginInterface`", Exception::INTERFACE_ERROR);
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

    /**
     * 构建 Http 请求客户端
     *
     * @return Client
     */
    private static function buildHttp($real_http_send, $request): Client
    {
        // 构建 MockHandler，用于模拟请求
        $mock = null;
        if ($real_http_send) {
            $mock = new MockHandler([
                new Response(200, $request->getHeaders(), $request->getBody())
            ]);
        }

        // 构建 HandlerStack
        $stack = HandlerStack::create($mock);

        $stack->push(self::defaultHeadersMiddleware());
//        $stack->push(self::logMiddleware());

        // 构建 Guzzle Client
        return new Client(['handler' => $stack]);
    }

    /**
     * 添加 Http 请求客户端中间件 - 自动拼装部分请求头
     *
     * @return callable
     */
    private static function defaultHeadersMiddleware(): callable
    {
        return Middleware::mapRequest(fn(RequestInterface $request) => $request
            ->withHeader('Accept', 'application/json, text/plain, application/x-gzip')
            ->withHeader('User-Agent', trim('Third-Party Processor ' . ($request->getHeader('User-Agent')[0] ?? '')))
        );
    }

    /**
     * 添加 Http 请求客户端中间件 - 日志记录
     *
     * @return callable
     */
    private static function logMiddleware(): callable
    {
        return Middleware::tap(function (RequestInterface $request, array $options) {
            dump('--------------------  Request --------------------', $request->getMethod(), $request->getUri());
            foreach ($request->getHeaders() as $name => $value) {
                dump("{$name}: ", $value);
            }
            dump('--------------------  end --------------------');
        });
    }
}