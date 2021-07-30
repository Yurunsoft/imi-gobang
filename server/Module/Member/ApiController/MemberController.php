<?php

namespace ImiApp\Module\Member\ApiController;

use Imi\Aop\Annotation\Inject;
use Imi\HttpValidate\Annotation\HttpValidation;
use Imi\RequestContext;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Session\Session;
use Imi\Validate\Annotation\Required;
use Imi\Validate\Annotation\Text;
use ImiApp\Module\Member\Annotation\LoginRequired;

/**
 * @Controller("/member/")
 */
class MemberController extends HttpController
{
    /**
     * @Inject("MemberService")
     *
     * @var \ImiApp\Module\Member\Service\MemberService
     */
    protected $memberService;

    /**
     * 注册.
     *
     * @Action
     * @Route(method="POST")
     *
     * @HttpValidation
     * @Required(name="username", message="用户名不能为空")
     * @Text(name="username", min=1, message="用户名不能为空")
     * @Required(name="password", message="密码不能为空")
     * @Text(name="password", min=1, message="密码不能为空")
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
     * 登录.
     *
     * @Action
     * @Route(method="POST")
     *
     * @HttpValidation
     * @Required(name="username", message="用户名不能为空")
     * @Text(name="username", min=1, message="用户名不能为空")
     * @Required(name="password", message="密码不能为空")
     * @Text(name="password", min=1, message="密码不能为空")
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
