<?php
declare (strict_types=1);

namespace app\controller;

use app\BaseController;
use app\server\LoginServer;
use app\server\RegisterServer;
use app\utils\TokenTool;
use think\annotation\route\Route;
use think\Response;

class Common extends BaseController
{
    use TokenTool;

    /**
     * 注册
     *
     * @return Response
     */
    #[Route("POST", "common/register")]
    public function register(): Response
    {
        $params = $this->request->getParams();

        // 检测用户是否存在
        $isExists = RegisterServer::getInstance()->isExistsUser($params);
        if ($isExists) {
            return $this->fail(-1, '用户名已存在');
        }

        // 创建用户
        $uid = RegisterServer::getInstance()->createUser($params);

        return $uid ? $this->success() : $this->fail();
    }

    /**
     * 登录
     *
     * @return Response
     */
    #[Route("POST", "common/login")]
    public function login(): Response
    {
        $params = $this->request->getParams();

        // 获取用户信息
        $userInfo = LoginServer::getInstance()->getUserInfo($params);
        if (empty($userInfo)) {
            return $this->fail(-1, '用户名或密码错误');
        }

        // 验证密码
        $result = LoginServer::getInstance()->passwordVerify($params['password'], $userInfo['password']);
        if (!$result) {
            return $this->fail(-1, '用户名或密码错误');
        }

        // 生成token数据
        $data = $this->generateTokenData($userInfo['id']);
        // 加密数据
        $token = $this->encryptRSA($data);

        return $this->success([
            'token' => $token
        ]);
    }

    /**
     * 退出
     *
     * @return Response
     */
    #[Route("POST", "common/logout")]
    public function logout(): Response
    {
        return $this->success();
    }
}
