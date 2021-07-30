<?php

namespace ImiApp\MainServer\WebSocketController;

use Imi\Server\WebSocket\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;

/**
 * 公共控制器.
 * @WSController
 */
class PublicController extends WebSocketController
{
    /**
     * ping.
     *
     * @WSAction
     * @WSRoute({"action"="ping"})
     *
     * @param array $data
     * @return void
     */
    public function ping($data)
    {
        return [
            'action'    =>  'pong',
        ];
    }
}
