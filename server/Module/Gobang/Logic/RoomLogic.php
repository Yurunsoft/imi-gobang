<?php
namespace ImiApp\Module\Gobang\Logic;

use Imi\Redis\Redis;
use Imi\Server\Server;
use Imi\ConnectContext;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use Imi\RequestContext;
use ImiApp\Exception\BusinessException;
use ImiApp\Module\Gobang\Enum\GobangStatus;
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
     * @Inject("MemberService")
     *
     * @var \ImiApp\Module\Member\Service\MemberService
     */
    protected $memberService;

    /**
     * @Inject("GobangService")
     *
     * @var \ImiApp\Module\Gobang\Service\GobangService
     */
    protected $gobangService;

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
        // 创建房间分组
        RequestContext::getServer()->createGroup('room:' . $room->getRoomId());
        // 加入房间
        $room = $this->join($memberId, $room->getRoomId());
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
        $room = null;
        $this->roomService->lock($roomId, function() use($memberId, $roomId, &$room){
            $room = $this->roomService->join($memberId, $roomId);
        });
        ConnectContext::set('roomId', $roomId);
        // 加入房间分组
        RequestContext::getServer()->joinGroup('room:' . $roomId, RequestContext::get('fd'));
        defer(function() use($roomId, $room){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
                'roomInfo'  =>  $room,
                // 'content'   =>  sprintf('%s 加入房间', $member->name),
            ]);
        });
        return $room;
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
        $room = null;
        $this->roomService->lock($roomId, function() use($memberId, $roomId, &$room){
            $room = $this->roomService->watch($memberId, $roomId);
        });
        // 加入房间分组
        RequestContext::getServer()->joinGroup('room:' . $roomId, RequestContext::get('fd'));
        defer(function() use($roomId, $room){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
                'roomInfo'  =>  $room,
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
        ConnectContext::set('roomId', null);
        // 离开房间分组
        RequestContext::getServer()->leaveGroup('room:' . $roomId, RequestContext::get('fd'));
        defer(function() use($roomId){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
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
        $room = $game = null;
        $this->roomService->lock($roomId, function() use($memberId, $roomId, &$room, &$game){
            $room = $this->roomService->ready($memberId, $roomId);
            if(GobangStatus::GAMING === $room->getStatus())
            {
                $game = $this->gobangService->create($roomId);
            }
        });
        defer(function() use($roomId, $room, $game){
            $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
                'roomInfo'  =>  $room,
                // 'content'   =>  sprintf('%s 已准备', $member->name),
            ]);
            $this->pushRoomMessage($roomId, MessageActions::GOBANG_INFO, [
                'game'  =>  $game,
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
            $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
                'roomInfo'  =>  $this->roomService->getInfo($roomId),
                // 'content'   =>  sprintf('%s 取消准备', $member->name),
            ]);
        });
    }

    /**
     * 当用户断开连接时
     *
     * @param integer $memberId
     * @return void
     */
    public function onMemberClose(int $memberId)
    {
        $roomId = ConnectContext::get('roomId', null, ConnectContext::getFdByFlag($memberId));
        if(!$roomId)
        {
            // 没有进入房间不处理
            return;
        }
        $this->roomService->lock($roomId, function() use($memberId, $roomId){
            $roomInfo = $this->roomService->getInfo($roomId);
            // 只有一人时，销毁房间
            if(1 === $roomInfo->getPerson())
            {
                $roomInfo->delete();
                $this->pushRooms();
                return;
            }
            if(GobangStatus::GAMING === $roomInfo->getStatus())
            {
                $roomInfo->setStatus(GobangStatus::WAIT_START);
                $roomInfo->setPlayer1Ready(false);
                $roomInfo->setPlayer2Ready(false);
                if($memberId === $roomInfo->getPlayerId1())
                {
                    $winnerMemberId = $roomInfo->getPlayerId2();
                }
                else
                {
                    $winnerMemberId = $roomInfo->getPlayerId1();
                }
                $winner = $this->memberService->get($winnerMemberId);
                $this->pushRoomMessage($roomId, MessageActions::GOBANG_INFO, [
                    'winner'    =>  $winner,
                ]);
            }
            if($memberId === $roomInfo->getPlayerId1())
            {
                $roomInfo->setPlayerId1(0);
            }
            else if($memberId === $roomInfo->getPlayerId2())
            {
                $roomInfo->setPlayerId2(0);
            }
            $roomInfo->save();
            $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
                'roomInfo'  =>  $this->roomService->getInfo($roomId),
                // 'content'   =>  sprintf('%s 离开房间', $member->name),
            ]);
        });
    }

}
