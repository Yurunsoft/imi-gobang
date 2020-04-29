<?php

use Imi\Log\LogLevel;
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'ImiApp\MainServer\HttpController',
        'ImiApp\MainServer\Aop',
        'ImiApp\Enum',
        'ImiApp\Module',
    ],
    'beans'    =>    [
        'SessionManager'    =>    [
            'handlerClass'    =>    \Imi\Server\Session\Handler\File::class,
        ],
        'SessionFile'    =>    [
            'savePath'    =>    dirname(__DIR__, 2) . '/.runtime/.session/',
        ],
        'SessionConfig'    =>    [

        ],
        'SessionCookie'    =>    [
            'lifetime'    =>    86400 * 30,
        ],
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                'OptionsMiddleware',
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                \Imi\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'HtmlView'    =>    [
            'templatePath'    =>    dirname(__DIR__) . '/template/',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        =>    [
                'tpl',
                'html',
                'php'
            ],
        ],
        'WebSocketDispatcher'    =>    [
            'middlewares'    =>    [
                'ReturnMessageMiddleware',
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
            ],
        ],
        'GroupRedis'    =>    [
            'redisPool'    =>    'redis',
        ],
        'ServerGroup'   =>  [
            'status'        =>  false,
        ],
        'ConnectContextStore'   =>  [
            'handlerClass'  =>  \Imi\Server\ConnectContext\StoreHandler\Local::class,
        ],
        'ConnectContextLocal'    =>    [
            'lockId'    =>  'redis',
        ],
        'OptionsMiddleware' =>  [
            // 设置允许的 Origin，为 null 时允许所有，为数组时允许多个
            'allowOrigin'       =>  null,
            // 允许的请求头
            'allowHeaders'      =>  'Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie, x-session-id',
            // 允许的跨域请求头
            'exposeHeaders'     =>  'Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie, x-session-id',
            // 允许的请求方法
            'allowMethods'      =>  'GET, POST, PATCH, PUT, DELETE',
            // 是否允许跨域 Cookie
            'allowCredentials'  =>  'true',
            // 当请求为 OPTIONS 时，是否中止后续中间件和路由逻辑，一般建议设为 true
            'optionsBreak'      =>  true,
        ],
        'HttpSessionMiddleware' =>  [
            'sessionIdHandler'    =>    function(\Imi\Server\Http\Message\Request $request){
                $sessionId = $request->getHeaderLine('X-Session-Id');
                if(!$sessionId)
                {
                    $sessionId = $request->get('_sessionId');
                }
                return $sessionId;
            },
        ],
        'HttpErrorHandler'    =>    [
            'handler'   => \ImiApp\MainServer\ErrorHandler\HttpErrorHandler::class,
        ],
    ],
];