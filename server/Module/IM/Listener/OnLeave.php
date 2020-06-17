<?php
namespace ImiApp\Module\IM\Listener;

use Imi\Server\Server;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Listener;
use ImiApp\Module\IM\Enum\MessageType;
use ImiApp\Module\IM\Enum\MessageActions;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;

/**
 * @Listener("IMI.SERVER.GROUP.LEAVE")
 */
class OnLeave implements IEventListener
{
    /**
     * @Inject("MemberService")
     *
     * @var \ImiApp\Module\Member\Service\MemberService
     */
    protected $memberService;

    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $data = $e->getData();
        $memberId = ConnectContext::get('memberId', null, $data['fd']);
        if($memberId)
        {
            $member = $this->memberService->get($memberId);
            Server::sendToGroup($data['groupName'], [
                'action'    =>  MessageActions::IM_RECEIVE,
                'sender'    =>  '系统消息',
                'type'      =>  MessageType::SYSTEM,
                'content'   =>  $member->username . ' 离开了',
                'time'      =>  date('Y-m-d H:i:s'),
            ]);
        }
    }

}
