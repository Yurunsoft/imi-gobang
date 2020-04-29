<?php
namespace ImiApp\Module\Member\Service;

use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use Imi\Server\Session\Session;

/**
 * @Bean("MemberSessionService")
 */
class MemberSessionService
{
    /**
     * @Inject("MemberService")
     *
     * @var \ImiApp\Module\Member\Service\MemberService
     */
    protected $memberService;

    /**
     * 用户ID
     *
     * @var int
     */
    protected $memberId;

    /**
     * 用户信息
     *
     * @var \ImiApp\Module\Member\Model\Member
     */
    protected $memberInfo;

    /**
     * 是否登录
     *
     * @var boolean
     */
    protected $isLogin = false;

    public function __init()
    {
        $this->init();
    }

    /**
     * 初始化
     *
     * @return void
     */
    public function init()
    {
        $memberId = Session::get('memberId');
        if(!$memberId)
        {
            return;
        }
        $this->memberId = $memberId;
        $this->isLogin = true;
    }

    /**
     * 是否登录
     *
     * @return boolean
     */
    public function isLogin()
    {
        return $this->isLogin;
    }

    /**
     * Get 用户信息
     *
     * @return \AccountService\Module\Account\Model\Member
     */ 
    public function getMemberInfo()
    {
        if(!$this->memberInfo)
        {
            $this->memberInfo = $this->memberService->get($this->memberId);
        }
        return $this->memberInfo;
    }

    /**
     * Get 用户ID
     *
     * @return int
     */ 
    public function getMemberId()
    {
        return $this->memberId;
    }

}
