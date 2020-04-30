<?php
namespace ImiApp\Module\Gobang\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class MessageActions extends BaseEnum
{
    /**
     * @EnumItem("房间创建")
     */
    const ROOM_CREATE = 'room.create';

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
     * @EnumItem("房间信息推送")
     */
    const ROOM_INFO = 'room.info';

    /**
     * @EnumItem("取消准备")
     */
    const ROOM_CANCEL_READY = 'room.cancelReady';

    /**
     * @EnumItem("落子结果")
     */
    const GOBANG_GO_RESULT = 'gobang.goResult';

    /**
     * @EnumItem("落子结果通知")
     */
    const GOBANG_RESULT_NOTIFY = 'gobang.resultNotify';

}
