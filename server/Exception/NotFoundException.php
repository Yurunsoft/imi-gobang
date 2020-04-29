<?php
namespace ImiApp\Exception;

use ImiApp\Enum\MessageCode;

/**
 * 未找到记录的异常类
 */
class NotFoundException extends BusinessException
{
    public function __construct($message = '记录未找到', $code = MessageCode::NOT_FOUND, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
