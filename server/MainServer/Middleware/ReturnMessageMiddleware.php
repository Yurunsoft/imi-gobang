<?php
namespace ImiApp\MainServer\Middleware;

use ImiApp\Enum\MessageCode;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use ImiApp\Exception\BusinessException;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Middleware\IMiddleware;

/**
 * 返回数据处理中间件
 * @Bean("ReturnMessageMiddleware")
 */
class ReturnMessageMiddleware implements IMiddleware
{
    /**
     * @Inject("ErrorLog")
     *
     * @var \Imi\Log\ErrorLog
     */
    protected $errorLog;

    public function process(IFrame $frame, IMessageHandler $handler)
    {
        try {
            $result = $handler->handle($frame);
        } catch(BusinessException $be) {
            $code = $be->getCode() ?? MessageCode::ERROR;
            $message = $be->getMessage();
            $result = [];
            $this->errorLog->onException($be);
        } catch(\Throwable $th) {
            $code = MessageCode::ERROR;
            $message = '系统错误';
            $result = [];
            $this->errorLog->onException($th);
        }
        if(null !== $result)
        {
            if(!isset($result['code']))
            {
                $result['code'] = $code ?? MessageCode::SUCCESS;
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
