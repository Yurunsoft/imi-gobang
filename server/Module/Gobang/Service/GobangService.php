<?php
namespace ImiApp\Module\Gobang\Service;

use Imi\Bean\Annotation\Bean;
use ImiApp\Exception\BusinessException;
use ImiApp\Module\Gobang\Enum\GobangColor;
use ImiApp\Module\Gobang\Model\GobangGameModel;

/**
 * @Bean("GobangService")
 */
class GobangService
{
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
            throw new BusinessException('战局不存在');
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
            $gobangGame->player1Color = GobangColor::BLACK_PIECE;
            $gobangGame->player2Color = GobangColor::WHITE_PIECE;
        }
        else
        {
            $gobangGame->player1Color = GobangColor::WHITE_PIECE;
            $gobangGame->player2Color = GobangColor::BLACK_PIECE;
        }
        $gobangGame->currentPiece = GobangColor::BLACK_PIECE;
        $gobangGame->save();
        return $gobangGame;
    }

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
        $game = $this->getByRoomId($roomId);
    }

}
