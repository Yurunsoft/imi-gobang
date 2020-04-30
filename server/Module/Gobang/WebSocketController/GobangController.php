<?php
namespace ImiApp\Module\Gobang\WebSocketController;

use Imi\Aop\Annotation\Inject;
use Imi\ConnectContext;
use Imi\Controller\WebSocketController;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;
use ImiApp\Module\Gobang\Enum\MessageActions;

/**
 * 五子棋控制器
 * @WSController
 */
class GobangController extends WebSocketController
{
    /**
     * @Inject("GobangLogic")
     *
     * @var \ImiApp\Module\Gobang\Logic\GobangLogic
     */
    protected $gobangLogic;

    /**
     * 落子
     *
     * @WSAction
     * @WSRoute({"action"="gobang.go"})
     * 
     * @param array $data
     * @return array
     */
    public function go($data)
    {
        $this->gobangLogic->go($data['roomId'], ConnectContext::get('memberId'), $data['x'], $data['y']);
        return [
            'action'    =>  MessageActions::GOBANG_GO,
        ];
    }

}