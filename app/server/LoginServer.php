<?php

namespace app\server;

use app\BaseServer;
use app\model\User;

class LoginServer extends BaseServer
{
    /**
     * 获取用户信息
     *
     * @param array $params
     * @return array
     */
    public function getUserInfo(array $params): array
    {
        $where = [
            ['account', '=', $params['account']]
        ];

        return User::getInstance()->info('id,password', $where);
    }

    /**
     * 验证密码
     *
     * @param string $password
     * @param string $oldPassword
     * @return bool
     */
    public function passwordVerify(string $password, string $oldPassword): bool
    {
        return password_verify($password, $oldPassword);
    }
}