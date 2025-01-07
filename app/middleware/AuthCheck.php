<?php
declare (strict_types=1);

namespace app\middleware;

use app\exceptions\UnauthorizedException;
use app\exceptions\UnauthorizedOperationException;
use app\model\Role;
use app\model\Rule;
use app\model\User;
use app\Request;
use app\utils\RequestTool;
use app\utils\TokenTool;
use Closure;
use think\Response;

/**
 * 权限检测中间件
 */
class AuthCheck
{
    use RequestTool;
    use TokenTool;

    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 判断是否在免登录名单中
        if (!$this->isInExemptLoginList()) {
            $token = $this->getToken();
            if (empty($token)) {
                throw new UnauthorizedException();
            }

            // 解析token
            $uid = $this->parseToken($token);

            // 检测是否是超级管理员
            if (!$this->isSuperAdmin($uid)) {
                $this->checkAuth($uid);
            }

            // 将用户ID设置到请求对象中
            $request->setUid($uid);
        }

        return $next($request);
    }

    /**
     * 权限检测
     *
     * @param int $uid
     * @return void
     */
    private function checkAuth(int $uid): void
    {
        // 判断是否在接口白名单中
        if (!$this->isInApiWhiteList()) {
            // 获取用户拥有的角色ID列表
            $roleIdList = User::getInstance()->getRoleIdList($uid);
            if (empty($roleIdList)) {
                throw new UnauthorizedOperationException();
            }

            // 获取角色拥有的权限ID列表
            $ruleIdList = Role::getInstance()->getRuleIdList($roleIdList);
            if (empty($ruleIdList)) {
                throw new UnauthorizedOperationException();
            }

            // 根据URL获取权限ID
            $url    = $this->getCurrentUrl();
            $ruleId = Rule::getInstance()->getRuleIdFromUrl($url);
            if (!in_array($ruleId, $ruleIdList)) {
                throw new UnauthorizedOperationException();
            }
        }
    }
}
