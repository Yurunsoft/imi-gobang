<?php
namespace ImiApp\MainServer\WebSocketController;

use Imi\Controller\WebSocketController;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;

/**
 * 公共控制器
 * @WSController
 */
class PublicController extends WebSocketController
{
    /**
     * ping
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
