<?php
namespace ImiApp\Module\IM\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class MessageType extends BaseEnum
{
    /**
     * @EnumItem("系统消息")
     */
    const SYSTEM = 1;

    /**
     * @EnumItem("聊天")
     */
    const CHAT = 2;

}
