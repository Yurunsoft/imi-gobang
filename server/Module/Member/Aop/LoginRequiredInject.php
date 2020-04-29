<?php
namespace ImiApp\Module\Member\Aop;

use Imi\Aop\JoinPoint;
use Imi\RequestContext;
use Imi\Aop\PointCutType;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use ImiApp\Module\Member\Exception\MemberNoLoginException;

/**
 * @Aspect
 */
class LoginRequiredInject
{
    /**
     * 自动事务支持
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \ImiApp\Module\Member\Annotation\LoginRequired::class
     *         }
     * )
     * @Before
     * @return mixed
     */
    public function inject(JoinPoint $joinPoint)
    {
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
		$memberSession = RequestContext::getBean('MemberSessionService');
        if(!$memberSession->isLogin())
        {
            throw new MemberNoLoginException;
        }
    }

}
