<?php
namespace ImiApp\Module\Gobang\Logic;

use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use ImiApp\Exception\BusinessException;
use ImiApp\Module\Gobang\Enum\GobangCell;
use ImiApp\Module\Gobang\Enum\MessageActions;

/**
 * @Bean("GobangLogic")
 */
class GobangLogic
{
    /**
     * @Inject("RoomService")
     *
     * @var \ImiApp\Module\Gobang\Service\RoomService
     */
    protected $roomService;

    /**
     * @Inject("GobangService")
     *
     * @var \ImiApp\Module\Gobang\Service\GobangService
     */
    protected $gobangService;

    /**
     * @Inject("RoomLogic")
     *
     * @var \ImiApp\Module\Gobang\Logic\RoomLogic
     */
    protected $roomLogic;

    /**
     * 落子
     *
     * @param integer $roomId
     * @param integer $memberId
     * @param integer $x
     * @param integer $y
     * @return void
     */
    public function go(int $roomId, int $memberId, int $x, int $y)
    {
        $game = $this->gobangService->go($roomId, $memberId, $x, $y);
        $data = [];
        $winner = $game->referee($x, $y);
        if(GobangCell::NONE !== $winner)
        {
            $room = $this->roomService->getInfo($winner);
            if($winner === $game->getPlayer1Color())
            {
                $winnerMemberId = $room->getPlayerId1();
            }
            else if($winner === $game->getPlayer2Color() && $winner === $room->getPlayerId2())
            {
                $winnerMemberId = $room->getPlayerId2();
            }
            else
            {
                throw new BusinessException('数据错误');
            }
            // TODO:用户信息$winnerMemberId
            // $data['content'] = sprintf('胜者 %s', )
        }
        // 棋盘
        $data['map'] = $game->getGobangMap();
        $this->roomLogic->pushRoomMessage($roomId, MessageActions::GOBANG_GO_RESULT_NOTIFY, $data);
    }

}
