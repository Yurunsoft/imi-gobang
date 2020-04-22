<?php
namespace ImiApp\MainServer\HttpController;

use Imi\Server\Route\Annotation\Action;
use Imi\Controller\SingletonHttpController;
use Imi\Server\Route\Annotation\Controller;

/**
 * @Controller("/member/")
 */
class MemberController extends SingletonHttpController
{
    /**
     * 注册
     * 
     * @Action
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function register(string $username, string $password)
    {

    }

    /**
     * 登录
     * 
     * @Action
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login(string $username, string $password)
    {

    }

}
