<?php
namespace ImiApp\Module\Gobang\Service;

use Imi\Config;
use Imi\Redis\Redis;
use Imi\Bean\Annotation\Bean;
use ImiApp\Exception\BusinessException;
use ImiApp\Module\Gobang\Enum\GobangStatus;
use ImiApp\Module\Gobang\Model\RoomModel;

/**
 * @Bean("RoomService")
 */
class RoomService
{
    /**
     * 获取房间信息
     *
     * @return \ImiApp\Module\Gobang\Model\RoomModel
     */
    public function getInfo(int $roomId): RoomModel
    {
        $record = RoomModel::find([
            'roomId'    =>  $roomId,
        ]);
        if(!$record)
        {
            throw new BusinessException('房间不存在');
        }
        return $record;
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
        $room = RoomModel::newInstance();
        $room->setCreatorId($memberId);
        $room->setTitle($title);
        $room->setRoomId(Redis::incr('imi:gobang:roomAtomic'));
        $room->save();
        return $room;
    }

    /**
     * 加入房间
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return \ImiApp\Module\Gobang\Model\RoomModel
     */
    public function join(int $memberId, int $roomId): RoomModel
    {
        $room = $this->getInfo($roomId);
        if(0 === $room->getPlayerId1())
        {
            $room->setPlayerId1($memberId);
        }
        else if(0 === $room->getPlayerId2())
        {
            $room->setPlayerId2($memberId);
        }
        else
        {
            throw new BusinessException('房间已满');
        }
        $room->save();
        return $room;
    }

    /**
     * 进入房间观战
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return \ImiApp\Module\Gobang\Model\RoomModel
     */
    public function watch(int $memberId, int $roomId): RoomModel
    {
        $room = $this->getInfo($roomId);
        $memberIds = &$room->getWatchMemberIds();
        $memberIds[] = $roomId;
        $room->save();
        return $room;
    }

    /**
     * 离开房间
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return \ImiApp\Module\Gobang\Model\RoomModel
     */
    public function leave(int $memberId, int $roomId): RoomModel
    {
        $room = $this->getInfo($roomId);
        if($memberId === $room->getPlayerId1())
        {
            $room->setPlayerId1(0);
        }
        else if($memberId === $room->getPlayerId2())
        {
            $room->setPlayerId2(0);
        }
        else
        {
            $watchMemberIds = &$room->getWatchMemberIds();
            if(false !== ($index = array_search($memberId, $watchMemberIds)))
            {
                throw new BusinessException('玩家已不在房间');
            }
            unset($watchMemberIds[$index]);
            $watchMemberIds = array_values($watchMemberIds);
        }
        $room->save();
        return $room;
    }

    /**
     * 准备
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return \ImiApp\Module\Gobang\Model\RoomModel
     */
    public function ready(int $memberId, int $roomId): RoomModel
    {
        $room = $this->getInfo($roomId);
        if($memberId === $room->getPlayerId1())
        {
            $room->setPlayer1Ready(true);
            $allReady = $room->getPlayer2Ready();
        }
        else if($memberId === $room->getPlayerId2())
        {
            $room->setPlayer2Ready(true);
            $allReady = $room->getPlayer1Ready();
        }
        else
        {
            throw new BusinessException('玩家不在房间');
        }
        if($allReady)
        {
            $room->setStatus(GobangStatus::GAMING);
        }
        $room->save();
        return $room;
    }

    /**
     * 取消准备
     *
     * @param integer $memberId
     * @param integer $roomId
     * @return \ImiApp\Module\Gobang\Model\RoomModel
     */
    public function cancelReady(int $memberId, int $roomId): RoomModel
    {
        $room = $this->getInfo($roomId);
        if(GobangStatus::WAIT_START !== $room->getStatus())
        {
            throw new BusinessException('状态不正确');
        }
        if($memberId === $room->getPlayerId1())
        {
            $room->setPlayer1Ready(false);
        }
        else if($memberId === $room->getPlayerId2())
        {
            $room->setPlayer2Ready(false);
        }
        else
        {
            throw new BusinessException('玩家不在房间');
        }
        $room->save();
        return $room;
    }

    /**
     * 房间操作加锁
     *
     * @param integer $roomId
     * @param callable $callback
     * @param callable $afterCallback
     * @return mixed
     */
    public function lock(int $roomId, callable $callback, $afterCallback = null)
    {
        $options = Config::get('@app.room.lock.options');
        $lock = new \Imi\Lock\Handler\Redis($roomId, $options);
        return $lock->lock($callback, $afterCallback);
    }

}
