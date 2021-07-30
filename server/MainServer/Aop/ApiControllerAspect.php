<?php

namespace ImiApp\MainServer\Aop;

use Imi\Aop\PointCutType;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AfterReturningJoinPoint;
use Imi\Aop\Annotation\AfterReturning;
use Imi\Server\Http\Route\Annotation\Action;

/**
 * @Aspect
 */
class ApiControllerAspect
{
    /**
     * 自动事务支持
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             Action::class
     *         }
     * )
     * @AfterReturning
     * @return mixed
     */
    public function parse(AfterReturningJoinPoint $joinPoint)
    {
        $returnValue = $joinPoint->getReturnValue();
        if (null === $returnValue || (is_array($returnValue) && ! isset($returnValue['code']))) {
            $returnValue['message'] = '';
            $returnValue['code'] = 0;
        } elseif (is_object($returnValue) && ! isset($returnValue->code)) {
            $returnValue->message = '';
            $returnValue->code = 0;
        } else {
            return;
        }
        $joinPoint->setReturnValue($returnValue);
    }
}
