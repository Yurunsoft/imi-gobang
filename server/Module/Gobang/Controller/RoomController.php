<?php
namespace ImiApp\Module\Gobang\Controller;

use Imi\ConnectContext;
use Imi\Aop\Annotation\Inject;
use Imi\Controller\WebSocketController;
use ImiApp\Module\Gobang\Enum\MessageActions;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;

/**
 * 房间控制器
 * @WSController
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
     * 获取房间列表
     *
     * @WSAction
     * @WSRoute({"action"="room.list"})
     * 
     * @param array $data
     * @return void
     */
    public function list($data)
    {
        $list = $this->roomLogic->getList();
        return [
            'action'    =>  'room.list',
            'list'      =>  $list,
        ];
    }

    /**
     * 创建房间
     *
     * @WSAction
     * @WSRoute({"action"="room.create"})
     *
     * @param array $data
     * @return void
     */
    public function create($data)
    {
        $roomId = $this->roomLogic->create(ConnectContext::get('memberId'), $data['title'] ?? null);
        return [
            'action'    =>  MessageActions::ROOM_CREATE,
            'roomId'    =>  $roomId,
        ];
    }

    /**
     * 加入房间
     *
     * @WSAction
     * @WSRoute({"action"="room.join"})
     *
     * @param array $data
     * @return void
     */
    public function join($data)
    {
        $this->roomLogic->join(ConnectContext::get('memberId'), $data['roomId']);
        return [
            'action'    =>  MessageActions::ROOM_JOIN,
        ];
    }

    /**
     * 进入房间观战
     *
     * @WSAction
     * @WSRoute({"action"="room.watch"})
     *
     * @param array $data
     * @return void
     */
    public function watch($data)
    {
        $this->roomLogic->watch(ConnectContext::get('memberId'), $data['roomId']);
        return [
            'action'    =>  MessageActions::ROOM_WATCH,
        ];
    }

    /**
     * 离开房间
     *
     * @WSAction
     * @WSRoute({"action"="room.leave"})
     *
     * @param array $data
     * @return void
     */
    public function leave($data)
    {
        $this->roomLogic->leave(ConnectContext::get('memberId'), $data['roomId']);
        return [
            'action'    =>  MessageActions::ROOM_LEAVE,
        ];
    }

    /**
     * 准备
     *
     * @WSAction
     * @WSRoute({"action"="room.ready"})
     * 
     * @param array $data
     * @return void
     */
    public function ready($data)
    {
        $this->roomLogic->ready(ConnectContext::get('memberId'), $data['roomId']);
        return [
            'action'    =>  MessageActions::ROOM_READY,
        ];
    }

    /**
     * 取消准备
     *
     * @WSAction
     * @WSRoute({"action"="room.cancelReady"})
     * 
     * @param array $data
     * @return void
     */
    public function cancelReady($data)
    {
        $this->roomLogic->cancelReady(ConnectContext::get('memberId'), $data['roomId']);
        return [
            'action'    =>  MessageActions::ROOM_CANCEL_READY,
        ];
    }

}