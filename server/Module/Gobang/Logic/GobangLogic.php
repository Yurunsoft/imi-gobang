<?php
namespace ImiApp\Module\Gobang\Logic;

use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;

/**
 * @Bean("GobangLogic")
 */
class GobangLogic
{
    /**
     * @Inject("GobangService")
     *
     * @var \ImiApp\Module\Gobang\Service\GobangService
     */
    protected $gobangService;

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
        $this->gobangService->go($roomId, $memberId, $x, $y);
    }

}
