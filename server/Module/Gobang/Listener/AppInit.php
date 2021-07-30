<?php

namespace ImiApp\Module\Gobang\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Redis\Redis;

/**
 * @Listener("IMI.APP.INIT")
 */
class AppInit implements IEventListener
{
    /**
     * 事件处理方法.
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e):void
    {
        Redis::del('imi:gobang:rooms');
        Redis::del('imi:gobang:games');
        Redis::del('imi:gobang:roomAtomic');
    }
}
