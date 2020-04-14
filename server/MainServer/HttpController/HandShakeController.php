<?php
namespace ImiApp\MainServer\HttpController;

use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\WebSocket\WSConfig;

/**
 * 测试
 * @Controller
 * @View(renderType="html")
 */
class HandShakeController extends HttpController
{
    /**
     * 
     * @Action
     * @Route("/ws")
     * @WSConfig(parserClass=\Imi\Server\DataParser\JsonObjectParser::class)
     * @return void
     */
    public function ws()
    {
        // 握手处理，什么都不做，框架会帮你做好
        
    }

}