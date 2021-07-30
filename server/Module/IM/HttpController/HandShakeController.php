<?php

declare(strict_types=1);

namespace ImiApp\Module\IM\HttpController;

use Imi\ConnectionContext;
use Imi\RequestContext;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Server;
use Imi\Server\View\Annotation\View;
use Imi\Server\WebSocket\Route\Annotation\WSConfig;
use ImiApp\Module\Member\Annotation\LoginRequired;

/**
 * 测试.
 *
 * @Controller
 * @View(renderType="html")
 */
class HandShakeController extends HttpController
{
    /**
     * @Action
     * @LoginRequired
     *
     * @Route("/im")
     * @WSConfig(parserClass=\Imi\Server\DataParser\JsonArrayParser::class)
     *
     * @return void
     */
    public function im()
    {
        // 握手处理，什么都不做，框架会帮你做好
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
        $memberSession = RequestContext::getBean('MemberSessionService');
        $memberId = $memberSession->getMemberId();
        ConnectionContext::set('memberId', $memberId);
        $flag = 'im-' . $memberId;
        $currentFd = ConnectionContext::getClientId();
        if (!ConnectionContext::bindNx($flag, $currentFd))
        {
            $fds = ConnectionContext::getClientIdByFlag($flag);
            if ($fds)
            {
                foreach ($fds as $fd)
                {
                    ConnectionContext::unbind($flag, $fd);
                }
                Server::close($fds);
            }
            if (!ConnectionContext::bindNx($flag, $currentFd))
            {
                Server::close($currentFd);
            }
        }
    }
}
