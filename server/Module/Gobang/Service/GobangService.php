<?php
namespace ImiApp\Module\Gobang\Service;

use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use ImiApp\Exception\BusinessException;
use ImiApp\Exception\NotFoundException;
use ImiApp\Module\Gobang\Enum\GobangCell;
use ImiApp\Module\Gobang\Model\GobangGameModel;

/**
 * @Bean("GobangService")
 */
class GobangService
{
    /**
     * @Inject("RoomService")
     *
     * @var \ImiApp\Module\Gobang\Service\RoomService
     */
    protected $roomService;

    /**
     * 获取战局
     *
     * @param int $roomId
     * @return \ImiApp\Module\Gobang\Model\GobangGameModel
     */
    public function getByRoomId(int $roomId): GobangGameModel
    {
        $record = GobangGameModel::find([
            'roomId'    =>  $roomId,
        ]);
        if(!$record)
        {
            throw new NotFoundException('战局不存在');
        }
        return $record;
    }

    /**
     * 创建战局
     *
     * @param int $roomId
     * @return \ImiApp\Module\Gobang\Model\GobangGameModel
     */
    public function create(int $roomId): GobangGameModel
    {
        $gobangGame = GobangGameModel::newInstance([
            'roomId'=>  $roomId,
            'size'  =>  15,
        ]);
        // 初始化棋盘
        $gobangGame->initMap();
        // 随机分配执子颜色
        if(1 === mt_rand(0, 1))
        {
            $gobangGame->player1Color = GobangCell::BLACK_PIECE;
            $gobangGame->player2Color = GobangCell::WHITE_PIECE;
        }
        else
        {
            $gobangGame->player1Color = GobangCell::WHITE_PIECE;
            $gobangGame->player2Color = GobangCell::BLACK_PIECE;
        }
        $gobangGame->currentPiece = GobangCell::BLACK_PIECE;
        $gobangGame->save();
        return $gobangGame;
    }

    /**
     * 落子，返回胜利方颜色
     *
     * @param integer $roomId
     * @param integer $memberId
     * @param integer $x
     * @param integer $y
     * @return \ImiApp\Module\Gobang\Model\GobangGameModel
     */
    public function go(int $roomId, int $memberId, int $x, int $y): GobangGameModel
    {
        $room = $this->roomService->getInfo($roomId);
        $game = $this->getByRoomId($roomId);
        $currentPiece = $game->getCurrentPiece();
        if($currentPiece === $game->getPlayer1Color() && $memberId !== $room->getPlayerId1())
        {
            throw new BusinessException('非法操作');
        }
        if($currentPiece === $game->getPlayer2Color() && $memberId !== $room->getPlayerId2())
        {
            throw new BusinessException('非法操作');
        }
        $game->setCell($x, $y, $currentPiece);
        if(GobangCell::BLACK_PIECE === $currentPiece)
        {
            $game->setCurrentPiece(GobangCell::WHITE_PIECE);
        }
        else
        {
            $game->setCurrentPiece(GobangCell::BLACK_PIECE);
        }
        $game->save();
        return $game;
    }

}
