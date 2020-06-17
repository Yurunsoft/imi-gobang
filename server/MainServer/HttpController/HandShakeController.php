<?php
namespace ImiApp\MainServer\HttpController;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use ImiApp\Module\Member\Annotation\LoginRequired;
use Imi\Server\Route\Annotation\WebSocket\WSConfig;

/**
 * 测试
 * @Controller
 * @View(renderType="html")
 */
class HandShakeController extends HttpController
{
    /**
     * @Action
     * @LoginRequired
     * 
     * @Route("/ws")
     * @WSConfig(parserClass=\Imi\Server\DataParser\JsonArrayParser::class)
     * @return void
     */
    public function ws()
    {
        // 握手处理，什么都不做，框架会帮你做好
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
        $memberSession = RequestContext::getBean('MemberSessionService');
        $memberId = $memberSession->getMemberId();
        ConnectContext::set('memberId', $memberId);
        $flag = 'ws-' . $memberId;
        $currentFd = $this->request->getSwooleRequest()->fd;
        if(!ConnectContext::bindNx($flag, $currentFd))
        {
            $fd = ConnectContext::getFdByFlag($flag);
            if($fd)
            {
                $this->request->getServerInstance()->getSwooleServer()->close($fd);
            }
            if(!ConnectContext::bindNx($flag, $currentFd))
            {
                $this->request->getServerInstance()->getSwooleServer()->close($currentFd);
            }
        }
    }

}