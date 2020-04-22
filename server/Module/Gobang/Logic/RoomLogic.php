<?php
namespace ImiApp\Module\Gobang\Logic;

use Imi\Redis\Redis;
use Imi\Server\Server;
use Imi\ConnectContext;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use ImiApp\Exception\BusinessException;
use ImiApp\Module\Gobang\Model\RoomModel;
use ImiApp\Module\Gobang\Enum\MessageActions;

/**
 * @Bean("RoomLogic")
 */
class RoomLogic
{
    /**
     * @Inject("RoomService")
     *
     * @var \ImiApp\Module\Gobang\Service\RoomService
     */
    protected $roomService;

    /**
     * 获取房间列表
     *
     * @return \ImiApp\Module\Gobang\Model\RoomModel[]
     */
    public function getList(): array
    {
        $list = Redis::hGetAll('imi:gobang:rooms');
        $result = [];
        foreach($list as $item)
        {
            $result[] = RoomModel::newInstance($item);
        }
        return $result;
    }

    /**
     * 创建房间，返回房间ID
     *
     * @param integer $memberId
     * @param string $title
     * @return \ImiApp\Module\Gobang\Model\RoomModel
     */
    public function create(int $memberId, string $title): RoomModel
    {
        $roomId = ConnectContext::get('roomId', null, ConnectContext::getFdByFlag($memberId));
        if($roomId)
        {
            throw new BusinessException('已在房间中，无法创建房间');
        }
        // 创建房间
        $room = $this->roomService->create($memberId, $title);
        // 加入房间
        $this->join($memberId, $room->getRoomId());
        // 推送房间列表
        defer(function(){
            $this->pushRooms();
        });
        return $room;
    }

    /**
     * 推送房间列表
     *
     * @return void
     */
    public function pushRooms()
    {
        Server::sendToGroup('rooms', [
            'action'    =>  MessageActions::ROOM_LIST,
            'list'      =>  $this->getList(),
        ]);
    }

    /**
     * 推送房间消息
     *
     * @param integer $roomId
     * @param string $action
     * @param array $data
     * @return void
     */
    public function pushRoomMessage(int $roomId, string $action, array $data = [])
    {
        $data['action'] = $action;
        Server::sendToGroup('room:' . $roomId, $data);
    }

    /**
     * 加入房间
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return void
     */
    public function join(int $memberId, int $roomId)
    {
        $this->roomService->lock($roomId, function() use($memberId, $roomId){
            $this->roomService->join($memberId, $roomId);
        });
        defer(function() use($roomId){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_JOIN, [
                'roomInfo'  =>  $this->roomService->getInfo($roomId),
                // 'content'   =>  sprintf('%s 加入房间', $member->name),
            ]);
        });
    }

    /**
     * 进入房间观战
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return void
     */
    public function watch(int $memberId, int $roomId)
    {
        $this->roomService->lock($roomId, function() use($memberId, $roomId){
            $this->roomService->watch($memberId, $roomId);
        });
        defer(function() use($roomId){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_WATCH, [
                'roomInfo'  =>  $this->roomService->getInfo($roomId),
                // 'content'   =>  sprintf('%s 进入观战', $member->name),
            ]);
        });
    }

    /**
     * 离开房间
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return void
     */
    public function leave(int $memberId, int $roomId)
    {
        $this->roomService->lock($roomId, function() use($memberId, $roomId){
            $this->roomService->leave($memberId, $roomId);
        });
        defer(function() use($roomId){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_LEAVE, [
                'roomInfo'  =>  $this->roomService->getInfo($roomId),
                // 'content'   =>  sprintf('%s 离开房间', $member->name),
            ]);
        });
    }

    /**
     * 准备
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return void
     */
    public function ready(int $memberId, int $roomId)
    {
        $this->roomService->lock($roomId, function() use($memberId, $roomId){
            $this->roomService->ready($memberId, $roomId);
        });
        defer(function() use($roomId){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_READY, [
                'roomInfo'  =>  $this->roomService->getInfo($roomId),
                // 'content'   =>  sprintf('%s 已准备', $member->name),
            ]);
        });
    }

    /**
     * 取消准备
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return void
     */
    public function cancelReady(int $memberId, int $roomId)
    {
        $this->roomService->lock($roomId, function() use($memberId, $roomId){
            $this->roomService->cancelReady($memberId, $roomId);
        });
        defer(function() use($roomId){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_CANCEL_READY, [
                'roomInfo'  =>  $this->roomService->getInfo($roomId),
                // 'content'   =>  sprintf('%s 取消准备', $member->name),
            ]);
        });
    }

}
