<?php
declare (strict_types = 1);

namespace app;

use app\middleware\AuthCheck;
use app\middleware\ParamValidate;
use app\utils\ResponseTool;
use think\App;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    use ResponseTool;

    /**
     * Request实例
     * @var Request
     */
    protected Request $request;

    /**
     * 应用实例
     * @var App
     */
    protected App $app;

    /**
     * 控制器中间件
     * @var array
     */
    protected array $middleware = [
        ParamValidate::class,// 请求参数验证中间件
        AuthCheck::class,// 权限检测中间件
    ];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = getRequest();

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }
}
