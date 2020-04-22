<?php
namespace ImiApp\Module\Gobang\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Redis\Redis;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;

/**
 * @Listener("IMI.MAIN_SERVER.WORKER.START.APP")
 */
class AppInit implements IWorkerStartEventListener
{
    /**
     * 事件处理方法
     * @param WorkerStartEventParam $e
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        Redis::del('imi:gobang:rooms');
        Redis::del('imi:gobang:games');
        Redis::del('imi:gobang:roomAtomic');
    }

}
