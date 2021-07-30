<?php

declare(strict_types=1);

namespace ImiApp\Module\Gobang\Logic;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Redis\Redis;
use Imi\RequestContext;
use Imi\Server\Server;
use ImiApp\Exception\BusinessException;
use ImiApp\Exception\NotFoundException;
use ImiApp\Module\Gobang\Enum\GobangStatus;
use ImiApp\Module\Gobang\Enum\MessageActions;
use ImiApp\Module\Gobang\Model\RoomModel;

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
     * 获取房间列表.
     *
     * @return \ImiApp\Module\Gobang\Model\RoomModel[]
     */
    public function getList(): array
    {
        $list = Redis::hGetAll('imi:gobang:rooms');
        $result = [];
        foreach ($list as $item)
        {
            $result[] = RoomModel::newInstance($item);
        }

        return $result;
    }

    /**
     * 创建房间，返回房间ID.
     */
    public function create(int $memberId, string $title): RoomModel
    {
        $roomId = ConnectionContext::get('roomId', null, ConnectionContext::getClientId());
        if ($roomId)
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
        $this->pushRooms();

        return $room;
    }

    /**
     * 推送房间列表.
     *
     * @return void
     */
    public function pushRooms()
    {
        Server::sendToGroup('rooms', [
            'action'    => MessageActions::ROOM_LIST,
            'list'      => $this->getList(),
        ]);
    }

    /**
     * 推送房间消息.
     *
     * @return void
     */
    public function pushRoomMessage(int $roomId, string $action, array $data = [])
    {
        $data['action'] = $action;
        Server::sendToGroup('room:' . $roomId, $data);
    }

    /**
     * 加入房间.
     */
    public function join(int $memberId, int $roomId): RoomModel
    {
        /** @var \ImiApp\Module\Gobang\Model\RoomModel $room */
        $room = null;
        $this->roomService->lock($roomId, function () use ($memberId, $roomId, &$room) {
            $room = $this->roomService->join($memberId, $roomId);
        });
        ConnectionContext::set('roomId', $roomId);
        // 加入房间分组
        RequestContext::getServer()->joinGroup('room:' . $roomId, RequestContext::get('clientId'));
        $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
            'roomInfo'  => $room,
            // 'content'   =>  sprintf('%s 加入房间', $member->name),
        ]);
        // 推送房间列表
        $this->pushRooms();

        return $room;
    }

    /**
     * 进入房间观战.
     */
    public function watch(int $memberId, int $roomId): RoomModel
    {
        /** @var \ImiApp\Module\Gobang\Model\RoomModel $room */
        $room = null;
        $this->roomService->lock($roomId, function () use ($memberId, $roomId, &$room) {
            $room = $this->roomService->watch($memberId, $roomId);
        });
        ConnectionContext::set('roomId', $roomId);
        if (GobangStatus::GAMING === $room->getStatus())
        {
            $game = $this->gobangService->getByRoomId($roomId);
        }
        else
        {
            $game = null;
        }
        // 加入房间分组
        RequestContext::getServer()->joinGroup('room:' . $roomId, RequestContext::get('clientId'));
        $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
            'roomInfo'  => $room,
            // 'content'   =>  sprintf('%s 进入观战', $member->name),
        ]);
        if ($game)
        {
            $this->pushRoomMessage($roomId, MessageActions::GOBANG_INFO, [
                'game'  => $game,
            ]);
        }
        // 推送房间列表
        $this->pushRooms();

        return $room;
    }

    /**
     * 离开房间.
     */
    public function leave(int $memberId, int $roomId): RoomModel
    {
        $room = null;
        $isDestoryRoom = false;
        $this->roomService->lock($roomId, function () use ($memberId, $roomId, &$room, &$isDestoryRoom) {
            $room = $this->roomService->leave($memberId, $roomId);
            // 只有一人时，销毁房间
            if ($room->getPerson() <= 0)
            {
                $isDestoryRoom = true;
                $room->delete();
                try
                {
                    $game = $this->gobangService->getByRoomId($roomId);
                    $game->delete();
                }
                catch (NotFoundException $ne)
                {
                }

                return;
            }
            if (GobangStatus::GAMING === $room->getStatus())
            {
                $room->setStatus(GobangStatus::WAIT_START);
                $room->setPlayer1Ready(false);
                $room->setPlayer2Ready(false);
                if (0 === $room->getPlayerId1())
                {
                    $winnerMemberId = $room->getPlayerId2();
                }
                else
                {
                    $winnerMemberId = $room->getPlayerId1();
                }
                $winner = $this->memberService->get($winnerMemberId);
                $room->save();
                $this->pushRoomMessage($roomId, MessageActions::GOBANG_INFO, [
                    'winner'    => $winner,
                ]);
            }
        });
        ConnectionContext::set('roomId', null);
        // 离开房间分组
        RequestContext::getServer()->leaveGroup('room:' . $roomId, RequestContext::get('clientId'));
        if ($isDestoryRoom)
        {
            // 房间销毁通知
            $this->pushRoomMessage($roomId, MessageActions::ROOM_DESTORY, [
            ]);
        }
        $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
            'roomInfo'  => $room,
            // 'content'   =>  sprintf('%s 离开房间', $member->name),
        ]);
        // 推送房间列表
        $this->pushRooms();

        return $room;
    }

    /**
     * 准备.
     *
     * @return void
     */
    public function ready(int $memberId, int $roomId)
    {
        $room = $game = null;
        $this->roomService->lock($roomId, function () use ($memberId, $roomId, &$room, &$game) {
            $room = $this->roomService->ready($memberId, $roomId);
            if (GobangStatus::GAMING === $room->getStatus())
            {
                $game = $this->gobangService->create($roomId);
            }
        });
        $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
            'roomInfo'  => $room,
            // 'content'   =>  sprintf('%s 已准备', $member->name),
        ]);
        $this->pushRoomMessage($roomId, MessageActions::GOBANG_INFO, [
            'game'  => $game,
        ]);
        // 推送房间列表
        $this->pushRooms();
    }

    /**
     * 取消准备.
     *
     * @return void
     */
    public function cancelReady(int $memberId, int $roomId)
    {
        $this->roomService->lock($roomId, function () use ($memberId, $roomId) {
            $this->roomService->cancelReady($memberId, $roomId);
        });
        $this->pushRoomMessage($roomId, MessageActions::ROOM_INFO, [
            'roomInfo'  => $this->roomService->getInfo($roomId),
            // 'content'   =>  sprintf('%s 取消准备', $member->name),
        ]);
        // 推送房间列表
        $this->pushRooms();
    }

    /**
     * 当用户断开连接时.
     *
     * @return void
     */
    public function onMemberClose(int $memberId)
    {
        $roomId = ConnectionContext::get('roomId', null, ConnectionContext::getClientId());
        if (!$roomId)
        {
            // 没有进入房间不处理
            return;
        }
        $this->leave($memberId, $roomId);
    }

    /**
     * Get the value of roomService.
     *
     * @return \ImiApp\Module\Gobang\Service\RoomService
     */
    public function getRoomService()
    {
        return $this->roomService;
    }
}
