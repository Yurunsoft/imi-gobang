<?php

declare(strict_types=1);

use Imi\App;

return [
    'configs'    => [
    ],
    // bean扫描目录
    // 'beanScan'    =>    [
    //     'ImiApp\MainServer\HttpController',
    //     'ImiApp\MainServer\WebSocketController',
    //     'ImiApp\MainServer\Aop',
    //     'ImiApp\MainServer\Middleware',
    //     'ImiApp\Enum',
    //     'ImiApp\Module',
    // ],
    'beans'    => [
        'SessionManager'    => [
            'handlerClass'    => \Imi\Server\Session\Handler\File::class,
        ],
        'SessionFile'    => [
            'savePath'    => dirname(__DIR__, 2) . '/.runtime/.session/',
        ],
        'SessionConfig'    => [
        ],
        'SessionCookie'    => [
            'enable'      => false,
            'lifetime'    => 86400 * 30,
        ],
        'HttpDispatcher'    => [
            'middlewares'    => (function () {
                $result = [
                    'OptionsMiddleware',
                    \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                ];

                $mode = App::isInited() ? App::getApp()->getType() : null;
                if ('swoole' === $mode)
                {
                    $result[] = \Imi\Swoole\Server\WebSocket\Middleware\HandShakeMiddleware::class;
                }

                $result[] = \Imi\Server\Http\Middleware\RouteMiddleware::class;

                return $result;
            })(),
        ],
        'HtmlView'    => [
            'templatePath'    => dirname(__DIR__) . '/template/',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        => [
                'tpl',
                'html',
                'php',
            ],
        ],
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                'ReturnMessageMiddleware',
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
            ],
        ],
        'OptionsMiddleware' => [
            // 设置允许的 Origin，为 null 时允许所有，为数组时允许多个
            'allowOrigin'       => null,
            // 允许的请求头
            'allowHeaders'      => 'Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie, x-session-id',
            // 允许的跨域请求头
            'exposeHeaders'     => 'Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie, x-session-id',
            // 允许的请求方法
            'allowMethods'      => 'GET, POST, PATCH, PUT, DELETE',
            // 是否允许跨域 Cookie
            'allowCredentials'  => 'true',
            // 当请求为 OPTIONS 时，是否中止后续中间件和路由逻辑，一般建议设为 true
            'optionsBreak'      => true,
        ],
        'HttpSessionMiddleware' => [
            'sessionIdHandler'    => function (Imi\Server\Http\Message\Request $request) {
                $sessionId = $request->getHeaderLine('X-Session-Id');
                if (!$sessionId)
                {
                    $sessionId = $request->get('_sessionId');
                }

                return $sessionId;
            },
        ],
        'HttpErrorHandler'    => [
            'handler'   => \ImiApp\MainServer\ErrorHandler\HttpErrorHandler::class,
        ],

        // 本地模式
        'ConnectionContextStore'   => [
            'handlerClass'  => \Imi\Server\ConnectionContext\StoreHandler\Local::class,
        ],
        'ConnectionContextLocal'    => [
            'lockId'    => null, // 非必设，可以用锁来防止数据错乱问题
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupLocal',
        ],

        // // Redis 模式
        // 'ConnectionContextStore'   =>  [
        //     'handlerClass'  =>  \Imi\Server\ConnectionContext\StoreHandler\Redis::class,
        // ],
        // 'ConnectionContextRedis'    =>    [
        //     'redisPool'    => 'redis', // Redis 连接池名称
        //     'redisDb'      => 0, // redis中第几个库
        //     'key'          => 'imi:gobang:connect_context', // 键
        //     'heartbeatTimespan' => 5, // 心跳时间，单位：秒
        //     'heartbeatTtl' => 8, // 心跳数据过期时间，单位：秒
        //     'dataEncode'=>  'serialize', // 数据写入前编码回调
        //     'dataDecode'=>  'unserialize', // 数据读出后处理回调
        //     'lockId'    =>  null, // 非必设，可以用锁来防止数据错乱问题
        // ],
        // 'ServerGroup' => [
        //     'groupHandler' => 'GroupRedis',
        // ],

        // 网关模式
        // 'ConnectionContextStore'   => [
        //     'handlerClass'  => 'ConnectionContextGateway',
        // ],
        // 'ServerGroup' => [
        //     'groupHandler' => 'GroupGateway',
        // ],
    ],
];
