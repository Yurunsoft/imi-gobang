<?php
namespace ImiApp\Module\IM\WebSocketController;

use Imi\Server\Server;
use Imi\RequestContext;
use Imi\Aop\Annotation\Inject;
use Imi\Controller\WebSocketController;
use ImiApp\Module\IM\Enum\MessageActions;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;

/**
 * IM 控制器
 * @WSController(route="/im")
 */
class IMController extends WebSocketController
{
    /**
     * @Inject("IMLogic")
     *
     * @var \ImiApp\Module\IM\Logic\IMLogic
     */
    protected $imLogic;

    /**
     * 加入房间
     *
     * @WSAction
     * @WSRoute({"action"="im.joinRoom"})
     * 
     * @param array $data
     * @return void
     */
    public function joinRoom($data)
    {
        $this->server->joinGroup('im.room.' . $data['roomId'], $this->frame->getFd());
        return [
            'action'    =>  MessageActions::IM_JOIN_ROOM,
        ];
    }

    /**
     * 发送内容
     *
     * @WSAction
     * @WSRoute({"action"="im.send"})
     * 
     * @param array $data
     * @return void
     */
    public function send($data)
    {
        $content = $data['content'] ?? '';
        if('' === $content)
        {
            return;
        }
        if(mb_strlen($content) > 128)
        {
            $content = mb_substr($content, 0, 128);
        }
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
        $memberSession = RequestContext::getBean('MemberSessionService');
        $sender = $memberSession->getMemberInfo()->username;
        $roomId = $data['roomId'];
        $time = date('Y-m-d H:i:s');
        defer(function() use($sender, $content, $time, $roomId){
            Server::sendToGroup('im.room.' . $roomId, [
                'action'    =>  MessageActions::IM_RECEIVE,
                'sender'    =>  $sender,
                'content'   =>  $content,
                'time'      =>  $time,
            ]);
        });
        return [
            'action'    =>  MessageActions::IM_SEND,
        ];
    }

}
