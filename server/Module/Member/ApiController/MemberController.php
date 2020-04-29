<?php
namespace ImiApp\Module\Member\ApiController;

use Imi\RequestContext;
use Imi\Aop\Annotation\Inject;
use Imi\Server\Session\Session;
use Imi\Validate\Annotation\Text;
use Imi\Validate\Annotation\Required;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Controller\SingletonHttpController;
use Imi\Server\Route\Annotation\Controller;
use Imi\HttpValidate\Annotation\HttpValidation;
use ImiApp\Module\Member\Annotation\LoginRequired;

/**
 * @Controller("/member/")
 */
class MemberController extends SingletonHttpController
{
    /**
     * @Inject("MemberService")
     *
     * @var \ImiApp\Module\Member\Service\MemberService
     */
    protected $memberService;

    /**
     * 注册
     *
     * @Action
     * @Route(method="POST")
     * 
     * @HttpValidation
     * @Required(name="username", message="用户名不能为空")
     * @Text(name="username", min="1", message="用户名不能为空")
     * @Required(name="password", message="密码不能为空")
     * @Text(name="password", min="1", message="密码不能为空")
     * 
     * @param string $username
     * @param string $password
     * @return void
     */
    public function register(string $username, string $password)
    {
        $this->memberService->register($username, $password);
    }

    /**
     * 登录
     *
     * @Action
     * @Route(method="POST")
     * 
     * @HttpValidation
     * @Required(name="username", message="用户名不能为空")
     * @Text(name="username", min="1", message="用户名不能为空")
     * @Required(name="password", message="密码不能为空")
     * @Text(name="password", min="1", message="密码不能为空")
     * 
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login(string $username, string $password)
    {
        $this->memberService->login($username, $password);
        return [
            'token' =>  Session::getID(),
        ];
    }

    /**
     * 登录状态
     *
     * @Action
     * @LoginRequired
     *
     * @return void
     */
    public function status()
    {
        /** @var \ImiApp\Module\Member\Service\MemberSessionService $memberSession */
        $memberSession = RequestContext::getBean('MemberSessionService');
        return [
            'data'  =>  $memberSession->getMemberInfo(),
        ];
    }

}
