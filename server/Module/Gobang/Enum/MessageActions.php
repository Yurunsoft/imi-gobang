<?php
namespace ImiApp\Module\Gobang\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class MessageActions extends BaseEnum
{
    /**
     * @EnumItem("房间列表")
     */
    const ROOM_LIST = 'room.list';

    /**
     * @EnumItem("加入房间")
     */
    const ROOM_JOIN = 'room.join';

    /**
     * @EnumItem("观战房间")
     */
    const ROOM_WATCH = 'room.watch';

    /**
     * @EnumItem("离开房间")
     */
    const ROOM_LEAVE = 'room.leave';

    /**
     * @EnumItem("准备")
     */
    const ROOM_READY = 'room.ready';

    /**
     * @EnumItem("取消准备")
     */
    const ROOM_CANCEL_READY = 'room.cancelReady';

}
