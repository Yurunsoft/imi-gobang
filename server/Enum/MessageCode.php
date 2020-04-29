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

    /**
     * @EnumItem("未找到记录")
     */
    const NOT_FOUND = 404;

    /**
     * @EnumItem("用户未登录")
     */
    const MEMBER_NOT_LOGIN = 1001;

}
