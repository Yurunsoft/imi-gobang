<?php
return [
    // 项目根命名空间
    'namespace'    =>    'ImiApp',

    // 配置文件
    'configs'    =>    [
        'beans'        =>    __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    =>    [
    ],

    // 组件命名空间
    'components'    =>  [
    ],

    // 主服务器配置
    'mainServer'    =>    [
        'namespace' =>  'ImiApp\MainServer',
        'type'      =>  Imi\Server\Type::WEBSOCKET,
        'host'      =>  '127.0.0.1',
        'port'      =>  8080,
        'mode'      =>  SWOOLE_BASE,
        'configs'   =>    [
            // 'worker_num'        =>  8,
            // 'task_worker_num'   =>  16,
        ],
    ],

    // 子服务器（端口监听）配置
    'subServers'        =>    [
        // 'SubServerName'   =>  [
        //     'namespace'    =>    'ImiApp\XXXServer',
        //     'type'        =>    Imi\Server\Type::HTTP,
        //     'host'        =>    '127.0.0.1',
        //     'port'        =>    13005,
        // ]
    ],

    // 连接池配置
    'pools'    =>    [
        // 主数据库
        'maindb'    =>    [
            'pool' => [
                // 同步池类名
                'syncClass'     =>    \Imi\Db\Pool\SyncDbPool::class,
                // 协程池类名
                'asyncClass'    =>    \Imi\Db\Pool\CoroutineDbPool::class,
                // 连接池配置
                'config' => [
                    // 池子中最多资源数
                    'maxResources' => 16,
                    // 池子中最少资源数
                    'minResources' => 1,
                ],
            ],
            // 连接池资源配置
            'resource' => [
                'host'        => '127.0.0.1',
                'port'        => 3306,
                'username'    => 'root',
                'password'    => 'root',
                'database'    => 'db_gobang',
                'charset'     => 'utf8mb4',
                'options'   =>  [
                    \PDO::ATTR_STRINGIFY_FETCHES    =>  false,
                    \PDO::ATTR_EMULATE_PREPARES     =>  false,
                ],
            ],
        ],
        'redis'    =>    [
            'pool' => [
                // 同步池类名
                'syncClass'     =>    \Imi\Redis\SyncRedisPool::class,
                // 协程池类名
                'asyncClass'    =>    \Imi\Redis\CoroutineRedisPool::class,
                'config' => [
                    // 池子中最多资源数
                    'maxResources' => 16,
                    // 池子中最少资源数
                    'minResources' => 1,
                ],
            ],
            // 数组资源配置
            'resource' => [
                'host'    =>    '127.0.0.1',
                'port'    =>    6379,
                // 是否自动序列化变量
                'serialize'    =>    true,
                // 密码
                'password'    =>    null,
                // 第几个库
                'db'    =>    0,
            ],
        ],
        'redisNoSerialize'    =>    [
            'pool' => [
                // 同步池类名
                'syncClass'     =>    \Imi\Redis\SyncRedisPool::class,
                // 协程池类名
                'asyncClass'    =>    \Imi\Redis\CoroutineRedisPool::class,
                'config' => [
                    // 池子中最多资源数
                    'maxResources' => 16,
                    // 池子中最少资源数
                    'minResources' => 1,
                ],
            ],
            // 数组资源配置
            'resource' => [
                'host'    =>    '127.0.0.1',
                'port'    =>    6379,
                // 是否自动序列化变量
                'serialize'    =>    false,
                // 密码
                'password'    =>    null,
                // 第几个库
                'db'    =>    0,
            ],
        ],
    ],

    // 数据库配置
    'db'    =>    [
        // 数默认连接池名
        'defaultPool'    =>    'maindb',
    ],

    // redis 配置
    'redis' =>  [
        // 数默认连接池名
        'defaultPool'   =>  'redis',
    ],

    // 锁
    'lock'  =>[
        'list'  =>  [
            'redis' =>  [
                'class' =>  'RedisLock',
                'options'   =>  [
                    'poolName'  =>  'redis',
                ],
            ],
        ]
    ],

    'room'  =>  [
        'lock'  =>  [
            'options'   =>  [
                'waitTimeout'   =>  10000,
                'lockExpire'    =>  10000,
                'keyPrefix'     =>  'imi:gobang:lock:'
            ],
        ],
    ],

    'tools'  =>  [
        'generate/model'    =>  [
            'namespace' =>  [
                'ImiApp\Module\Member\Model' =>  [
                    'tables'    =>  [
                        'tb_member',
                    ],
                ],
            ]
        ],
    ],
];