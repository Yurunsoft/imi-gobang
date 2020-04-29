<?php
namespace ImiApp\Module\Member\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 用户登录状态验证注解
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class LoginRequired extends Base
{

}
