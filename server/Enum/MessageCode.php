<?php
namespace ImiApp\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class MessageCode extends BaseEnum
{
    /**
     * @EnumItem("成功")
     */
    const SUCCESS = 0;

    /**
     * @EnumItem("错误")
     */
    const ERROR = 500;

}
