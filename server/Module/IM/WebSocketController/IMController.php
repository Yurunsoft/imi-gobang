<?php
namespace ImiApp\Module\IM\WebSocketController;

use Imi\Server\Server;
use Imi\RequestContext;
use Imi\Aop\Annotation\Inject;
use ImiApp\Module\IM\Enum\MessageType;
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
        $group = 'im.room.' . $data['roomId'];
        $this->server->joinGroup($group, $this->frame->getFd());
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
        $memberSession = RequestContext::getBean('MemberSessionService');
        Server::sendToGroup($group, [
            'action'    =>  MessageActions::IM_RECEIVE,
            'sender'    =>  '系统消息',
            'type'      =>  MessageType::SYSTEM,
            'content'   =>  $memberSession->getMemberInfo()->username . ' 进来了',
            'time'      =>  date('Y-m-d H:i:s'),
        ]);
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
                'type'      =>  MessageType::CHAT,
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
