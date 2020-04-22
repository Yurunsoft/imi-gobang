<?php
namespace ImiApp\Module\Gobang\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class GobangCell extends BaseEnum
{
    /**
     * @EnumItem("空")
     */
    const NONE = 0;

    /**
     * @EnumItem("黑棋")
     */
    const BLACK_PIECE = 1;

    /**
     * @EnumItem("白棋")
     */
    const WHITE_PIECE = 2;

}
