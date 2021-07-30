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
 * 房间控制器.
 * @WSController(route="/ws")
 */
class RoomController extends WebSocketController
{
    /**
     * @Inject("RoomLogic")
     *
     * @var \ImiApp\Module\Gobang\Logic\RoomLogic
     */
    protected $roomLogic;

    /**
     * 获取房间列表.
     *
     * @WSAction
     * @WSRoute({"action"="room.list"})
     *
     * @param array $data
     * @return void
     */
    public function list($data)
    {
        $this->server->joinGroup('rooms', $this->frame->getClientId());
        $list = $this->roomLogic->getList();

        return [
            'action'    =>  MessageActions::ROOM_LIST,
            'list'      =>  $list,
        ];
    }

    /**
     * 获取房间信息.
     *
     * @WSAction
     * @WSRoute({"action"="room.info"})
     *
     * @param array $data
     * @return void
     */
    public function info($data)
    {
        return [
            'action'    =>  MessageActions::ROOM_INFO,
            'roomInfo'  =>  $this->roomLogic->getRoomService()->getInfo($data['roomId']),
        ];
    }

    /**
     * 创建房间.
     *
     * @WSAction
     * @WSRoute({"action"="room.create"})
     *
     * @param array $data
     * @return void
     */
    public function create($data)
    {
        $roomInfo = $this->roomLogic->create(ConnectionContext::get('memberId'), $data['title'] ?? null);

        return [
            'action'    =>  MessageActions::ROOM_CREATE,
            'roomInfo'  =>  $roomInfo,
        ];
    }

    /**
     * 加入房间.
     *
     * @WSAction
     * @WSRoute({"action"="room.join"})
     *
     * @param array $data
     * @return void
     */
    public function join($data)
    {
        $roomInfo = $this->roomLogic->join(ConnectionContext::get('memberId'), $data['roomId']);

        return [
            'action'    =>  MessageActions::ROOM_JOIN,
            'roomInfo'  =>  $roomInfo,
        ];
    }

    /**
     * 进入房间观战.
     *
     * @WSAction
     * @WSRoute({"action"="room.watch"})
     *
     * @param array $data
     * @return void
     */
    public function watch($data)
    {
        $roomInfo = $this->roomLogic->watch(ConnectionContext::get('memberId'), $data['roomId']);

        return [
            'action'    =>  MessageActions::ROOM_WATCH,
            'roomInfo'  =>  $roomInfo,
        ];
    }

    /**
     * 离开房间.
     *
     * @WSAction
     * @WSRoute({"action"="room.leave"})
     *
     * @param array $data
     * @return void
     */
    public function leave($data)
    {
        $this->roomLogic->leave(ConnectionContext::get('memberId'), $data['roomId']);

        return [
            'action'    =>  MessageActions::ROOM_LEAVE,
        ];
    }

    /**
     * 准备.
     *
     * @WSAction
     * @WSRoute({"action"="room.ready"})
     *
     * @param array $data
     * @return void
     */
    public function ready($data)
    {
        $this->roomLogic->ready(ConnectionContext::get('memberId'), $data['roomId']);

        return [
            'action'    =>  MessageActions::ROOM_READY,
        ];
    }

    /**
     * 取消准备.
     *
     * @WSAction
     * @WSRoute({"action"="room.cancelReady"})
     *
     * @param array $data
     * @return void
     */
    public function cancelReady($data)
    {
        $this->roomLogic->cancelReady(ConnectionContext::get('memberId'), $data['roomId']);

        return [
            'action'    =>  MessageActions::ROOM_CANCEL_READY,
        ];
    }
}
