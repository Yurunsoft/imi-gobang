<?php

namespace ImiApp\Module\Gobang\WebSocketController;

use Imi\Aop\Annotation\Inject;
use Imi\ConnectionContext;
use Imi\Server\WebSocket\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;
use ImiApp\Module\Gobang\Enum\MessageActions;

/**
 * 五子棋控制器.
 * @WSController(route="/ws")
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
     * 落子.
     *
     * @WSAction
     * @WSRoute({"action"="gobang.go"})
     *
     * @param array $data
     * @return array
     */
    public function go($data)
    {
        $this->gobangLogic->go($data['roomId'], ConnectionContext::get('memberId'), $data['x'], $data['y']);

        return [
            'action'    =>  MessageActions::GOBANG_GO,
        ];
    }
}
