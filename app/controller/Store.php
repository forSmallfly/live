<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\middleware\AuthCheck;
use app\middleware\OauthVerify;
use app\middleware\ParamValidate;
use think\annotation\route\Route;
use think\Response;

class Store extends BaseController
{
    protected array $middleware = [
        ParamValidate::class,// 请求参数验证中间件
        AuthCheck::class,// 权限检测中间件
        OauthVerify::class,
    ];

    /**
     * @return Response
     */
    #[Route("POST", "store/link_store")]
    public function linkStore(): Response
    {
        return $this->success();
    }
}
