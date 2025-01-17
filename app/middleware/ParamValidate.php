<?php
declare (strict_types=1);

namespace app\middleware;

use app\exceptions\ParamValidateException;
use app\exceptions\ValidateNotFoundException;
use app\exceptions\ValidateSceneUndefinedException;
use app\Request;
use app\utils\RequestTool;
use app\utils\ValidateTool;
use Closure;
use think\Response;

/**
 * 请求参数验证中间件
 */
class ParamValidate
{
    use RequestTool;
    use ValidateTool;

    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 获取验证类
        $validateClass = $this->getValidateClass($request);
        // 空控制器不进行验证处理
        if ($validateClass == 'app\validate\Error') {
            return $next($request);
        }

        // 检测验证类是否存在
        if (!class_exists($validateClass)) {
            throw new ValidateNotFoundException($validateClass);
        }

        // 获取验证对象
        $validate = $this->getValidateObj($validateClass);
        $scene    = $request->action();
        // 检测验证场景是否定义
        if (!$validate->hasScene($scene)) {
            throw new ValidateSceneUndefinedException($validateClass, $scene);
        }

        // 获取请求参数
        $params = $this->getParams($request);
        // 针对当前场景进行参数验证
        if (!$validate->scene($scene)->check($params)) {
            throw new ParamValidateException($validate->getError());
        }

        // 过滤请求参数
        $params = $this->filterParams($scene, $validate, $params);

        // 将过滤后的请求参数设置到请求类
        $request->setParams($params);

        return $next($request);
    }
}
