<?php

declare(strict_types=1);

namespace ImiApp\Module\IM\WebSocketController;

use Imi\RequestContext;
use Imi\Server\Server;
use Imi\Server\WebSocket\Controller\WebSocketController;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;
use ImiApp\Module\IM\Enum\MessageActions;
use ImiApp\Module\IM\Enum\MessageType;

/**
 * IM 控制器.
 *
 * @WSController(route="/im")
 */
class IMController extends WebSocketController
{
    /**
     * 加入房间.
     *
     * @WSAction
     * @WSRoute({"action"="im.joinRoom"})
     *
     * @param array $data
     *
     * @return void
     */
    public function joinRoom($data)
    {
        $group = 'im.room.' . $data['roomId'];
        $this->server->joinGroup($group, $this->frame->getClientId());
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
        $memberSession = RequestContext::getBean('MemberSessionService');
        Server::sendToGroup($group, [
            'action'    => MessageActions::IM_RECEIVE,
            'sender'    => '系统消息',
            'type'      => MessageType::SYSTEM,
            'content'   => $memberSession->getMemberInfo()->username . ' 进来了',
            'time'      => date('Y-m-d H:i:s'),
        ]);

        return [
            'action'    => MessageActions::IM_JOIN_ROOM,
        ];
    }

    /**
     * 发送内容.
     *
     * @WSAction
     * @WSRoute({"action"="im.send"})
     *
     * @param array $data
     *
     * @return void
     */
    public function send($data)
    {
        $content = $data['content'] ?? '';
        if ('' === $content)
        {
            return;
        }
        if (mb_strlen($content) > 128)
        {
            $content = mb_substr($content, 0, 128);
        }
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
        $memberSession = RequestContext::getBean('MemberSessionService');
        $sender = $memberSession->getMemberInfo()->username;
        $roomId = $data['roomId'];
        $time = date('Y-m-d H:i:s');
        Server::sendToGroup('im.room.' . $roomId, [
            'action'    => MessageActions::IM_RECEIVE,
            'type'      => MessageType::CHAT,
            'sender'    => $sender,
            'content'   => $content,
            'time'      => $time,
        ]);

        return [
            'action'    => MessageActions::IM_SEND,
        ];
    }
}
