<?php
namespace ImiApp\Module\Gobang\Listener;

use Imi\Redis\Redis;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener("IMI.APP.INIT")
 */
class AppInit implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        Redis::del('imi:gobang:rooms');
        Redis::del('imi:gobang:games');
        Redis::del('imi:gobang:roomAtomic');
    }

}
