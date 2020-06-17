<?php
namespace ImiApp\Module\Gobang\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\RedisEntity;
use Imi\Model\RedisModel;
use ImiApp\Module\Gobang\Enum\GobangCell;

/**
 * 五子棋游戏模型
 * 
 * @RedisEntity(storage="hash", key="imi:gobang:games", member="{roomId}")
 */
class GobangGameModel extends RedisModel
{
    /**
     * 房间ID
     *
     * @Column
     *
     * @var int
     */
    protected $roomId;

    /**
     * 玩家1棋子颜色
     *
     * @Column
     *
     * @var int
     */
    protected $player1Color;

    /**
     * 玩家2棋子颜色
     *
     * @Column
     *
     * @var int
     */
    protected $player2Color;

    /**
     * 棋盘尺寸
     *
     * @Column
     *
     * @var int
     */
    protected $size;

    /**
     * 棋盘
     *
     * @Column
     * 
     * @var array
     */
    protected $gobangMap;

    /**
     * 当前出子颜色
     *
     * @Column
     *
     * @var int
     */
    protected $currentPiece;

    /**
     * 最后落子的坐标X
     *
     * @Column
     *
     * @var int
     */
    protected $lastGoX;

    /**
     * 最后落子的坐标Y
     *
     * @Column
     *
     * @var int
     */
    protected $lastGoY;

    /**
     * 初始化棋盘
     *
     * @return void
     */
    public function initMap()
    {
        $this->gobangMap = [];
        for($i = 0; $i < $this->size; ++$i)
        {
            for($j = 0; $j < $this->size; ++$j)
            {
                $this->gobangMap[$i][$j] = GobangCell::NONE;
            }
        }
    }

    /**
     * 设置棋盘
     *
     * @param integer $x
     * @param integer $y
     * @param integer $value
     * @return void
     */
    public function setCell(int $x, int $y, int $value)
    {
        $this->gobangMap[$x][$y] = $value;
        $this->lastGoX = $x;
        $this->lastGoY = $y;
    }

    /**
     * 判断输赢，传入最后下子位置
     * 返回胜利方颜色
     *
     * @param integer $x
     * @param integer $y
     * @return integer
     */
    public function referee(int $x, int $y): int
    {
        $color = $this->gobangMap[$x][$y] ?? GobangCell::NONE;
        if(GobangCell::NONE === $color)
        {
            return GobangCell::NONE;
        }
        static $directionRules = [
            'leftRight'             =>  ['x' => 1, 'y' => 0],
            'upDown'                =>  ['x' => 0, 'y' => 1],
            'LeftUpperRightLower'   =>  ['x' => -1, 'y' => -1],
            'RightUpperLowerLeft'   =>  ['x' => 1, 'y' => -1],
        ];
        foreach($directionRules as $directionRule)
        {
            $pieceCount = 1;
            $xStep = $directionRule['x'];
            $yStep = $directionRule['y'];
            foreach([1, -1] as $num)
            {
                for($i = 1; $i < 5; ++$i)
                {
                    $tmpX = $x + $xStep * $i * $num;
                    $tmpY = $y + $yStep * $i * $num;
                    if($color === ($this->gobangMap[$tmpX][$tmpY] ?? GobangCell::NONE))
                    {
                        if(++$pieceCount >= 5)
                        {
                            return $color;
                        }
                    }
                    else
                    {
                        break;
                    }
                }
            }
        }
        return GobangCell::NONE;
    }

    /**
     * Get 玩家1棋子颜色
     *
     * @return int
     */ 
    public function getPlayer1Color()
    {
        return $this->player1Color;
    }

    /**
     * Set 玩家1棋子颜色
     *
     * @param int $player1Color  玩家1棋子颜色
     *
     * @return self
     */ 
    public function setPlayer1Color(int $player1Color)
    {
        $this->player1Color = $player1Color;

        return $this;
    }

    /**
     * Get 玩家2棋子颜色
     *
     * @return int
     */ 
    public function getPlayer2Color()
    {
        return $this->player2Color;
    }

    /**
     * Set 玩家2棋子颜色
     *
     * @param int $player2Color  玩家2棋子颜色
     *
     * @return self
     */ 
    public function setPlayer2Color(int $player2Color)
    {
        $this->player2Color = $player2Color;

        return $this;
    }

    /**
     * Get 棋盘尺寸
     *
     * @return int
     */ 
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set 棋盘尺寸
     *
     * @param int $size  棋盘尺寸
     *
     * @return self
     */ 
    public function setSize(int $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get 棋盘
     *
     * @return array
     */ 
    public function &getGobangMap()
    {
        return $this->gobangMap;
    }

    /**
     * Set 棋盘
     *
     * @param array $gobangMap  棋盘
     *
     * @return self
     */ 
    public function setGobangMap(array $gobangMap)
    {
        $this->gobangMap = $gobangMap;

        return $this;
    }

    /**
     * Get 当前出子颜色
     *
     * @return int
     */ 
    public function getCurrentPiece()
    {
        return $this->currentPiece;
    }

    /**
     * Set 当前出子颜色
     *
     * @param int $currentPiece  当前出子颜色
     *
     * @return self
     */ 
    public function setCurrentPiece(int $currentPiece)
    {
        $this->currentPiece = $currentPiece;

        return $this;
    }

    /**
     * Get 房间ID
     *
     * @return int
     */ 
    public function getRoomId()
    {
        return $this->roomId;
    }

    /**
     * Set 房间ID
     *
     * @param int $roomId  房间ID
     *
     * @return self
     */ 
    public function setRoomId(int $roomId)
    {
        $this->roomId = $roomId;

        return $this;
    }

    /**
     * Get 最后落子的坐标X
     *
     * @return int
     */ 
    public function getLastGoX()
    {
        return $this->lastGoX;
    }

    /**
     * Set 最后落子的坐标X
     *
     * @param int $lastGoX  最后落子的坐标X
     *
     * @return self
     */ 
    public function setLastGoX(?int $lastGoX)
    {
        $this->lastGoX = $lastGoX;

        return $this;
    }

    /**
     * Get 最后落子的坐标Y
     *
     * @return int
     */ 
    public function getLastGoY()
    {
        return $this->lastGoY;
    }

    /**
     * Set 最后落子的坐标Y
     *
     * @param int $lastGoY  最后落子的坐标Y
     *
     * @return self
     */ 
    public function setLastGoY(?int $lastGoY)
    {
        $this->lastGoY = $lastGoY;

        return $this;
    }

}
