<?php

declare(strict_types=1);

namespace ImiApp\Module\Gobang\Listener;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\ConnectionContext;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\Uri;

/**
 * @ClassEventListener(className="Imi\Swoole\Server\WebSocket\Server", eventName="close")
 * @Listener("IMI.WORKERMAN.SERVER.CLOSE")
 */
class OnClose implements IEventListener
{
    /**
     * @Inject("RoomLogic")
     *
     * @var \ImiApp\Module\Gobang\Logic\RoomLogic
     */
    protected $roomLogic;

    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $uri = new Uri(ConnectionContext::get('uri'));
        if ('/ws' === $uri->getPath())
        {
            $memberId = ConnectionContext::get('memberId');
            if ($memberId)
            {
                $this->roomLogic->onMemberClose($memberId);
            }
        }
    }
}
