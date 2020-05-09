<?php
namespace ImiApp\Module\IM\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class MessageActions extends BaseEnum
{
    /**
     * @EnumItem("加入房间")
     */
    const IM_JOIN_ROOM = 'im.joinRoom';

    /**
     * @EnumItem("发送内容")
     */
    const IM_SEND = 'im.send';

    /**
     * @EnumItem("接收内容")
     */
    const IM_RECEIVE = 'im.receive';

}
