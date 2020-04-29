<?php
namespace ImiApp\Exception;

use ImiApp\Enum\MessageCode;

/**
 * 业务异常类
 */
class BusinessException extends \Exception
{
    public function __construct($message = '网络错误', $code = MessageCode::ERROR, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
