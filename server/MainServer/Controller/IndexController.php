<?php
namespace ImiApp\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Controller\WebSocketController;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;
use Imi\Server\Route\Annotation\WebSocket\WSMiddleware;

/**
 * 数据收发测试
 * @WSController
 */
class IndexController extends WebSocketController
{
    /**
     * 发送消息
     *
     * @WSAction
     * @WSRoute({"action"="send"})
     * @param 
     * @return void
     */
    public function send($data)
    {
        $clientInfo = $this->server->getSwooleServer()->getClientInfo($this->frame->getFd());
        $message = '[' . ($clientInfo['remote_ip'] ?? '') . ':' . ($clientInfo['remote_port'] ?? '') . ']: ' . $data->message;
        return [
            'success'   =>  true,
            'data'      =>  $message,
        ];
    }

}