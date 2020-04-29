<?php
namespace ImiApp\Module\Member\Exception;

use ImiApp\Enum\MessageCode;
use ImiApp\Exception\BusinessException;

/**
 * 用户未登录异常
 */
class MemberNoLoginException extends BusinessException
{
    public function __construct($message = '用户未登录', $code = MessageCode::MEMBER_NOT_LOGIN, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
