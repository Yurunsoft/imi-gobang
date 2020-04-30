<?php
namespace ImiApp\Module\Gobang\Listener;

use Imi\ConnectContext;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;

/**
 * @ClassEventListener(className="Imi\Server\WebSocket\Server", eventName="close")
 */
class OnClose implements ICloseEventListener
{
    /**
     * @Inject("RoomLogic")
     *
     * @var \ImiApp\Module\Gobang\Logic\RoomLogic
     */
    protected $roomLogic;

    /**
     * 事件处理方法
     * @param CloseEventParam $e
     * @return void
     */
    public function handle(CloseEventParam $e)
    {
        $memberId = ConnectContext::get('memberId');
        if($memberId)
        {
            $this->roomLogic->onMemberClose($memberId);
        }
    }

}
