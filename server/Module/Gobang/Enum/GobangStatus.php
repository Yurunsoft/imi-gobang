<?php
namespace ImiApp\Module\Gobang\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class GobangStatus extends BaseEnum
{
    /**
     * @EnumItem("等待开始")
     */
    const WAIT_START = 1;

    /**
     * @EnumItem("游戏中")
     */
    const GAMING = 2;

}
