<?php

namespace app\server;

use app\BaseServer;
use app\model\User;

class RegisterServer extends BaseServer
{
    /**
     * 检测用户是否存在
     *
     * @param array $params
     * @return bool
     */
    public function isExistsUser(array $params): bool
    {
        $where = [
            ['account', '=', $params['account']]
        ];

        $userInfo = User::getInstance()->info('id', $where);
        return !empty($userInfo);
    }

    /**
     * 创建用户
     *
     * @param array $params
     * @return int
     */
    public function createUser(array $params): int
    {
        $data = [
            'account'  => $params['account'],
            'password' => password_hash($params['password'], PASSWORD_DEFAULT),
            'mobile'   => $params['mobile'],
            'email'    => $params['email']
        ];

        return User::getInstance()->add($data, true);
    }
}