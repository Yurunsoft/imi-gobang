<?php
namespace ImiApp\MainServer\Middleware;

use Imi\Bean\Annotation\Bean;
use ImiApp\Exception\BusinessException;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Middleware\IMiddleware;
use ImiApp\Enum\MessageCode;

/**
 * 返回数据处理中间件
 * @Bean("ReturnMessageMiddleware")
 */
class ReturnMessageMiddleware implements IMiddleware
{
    public function process(IFrame $frame, IMessageHandler $handler)
    {
        try {
            $result = $handler->handle($frame);
        } catch(BusinessException $be) {
            $code = $be->getCode() ?? MessageCode::ERROR;
            $message = $be->getMessage();
            $result = [];
        } catch(\Throwable $th) {
            $code = MessageCode::ERROR;
            $message = '系统错误';
            $result = [];
        }
        if(null !== $result)
        {
            if(!isset($result['code']))
            {
                $result['code'] = $code ?? MessageCode::ERROR;
            }
            if(!isset($result['message']))
            {
                $result['message'] = $message ?? '';
            }
            if(!isset($result['messageId']))
            {
                $result['messageId'] = $frame->getFormatData()['messageId'] ?? null;
            }
            return $result;
        }
    }

}
