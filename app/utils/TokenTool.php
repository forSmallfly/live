<?php

namespace app\utils;

use app\exceptions\UnauthorizedException;
use Throwable;

/**
 * token工具库
 */
trait TokenTool
{
    use RsaTool;

    /**
     * 生成token数据
     *
     * @param int $uid
     * @return array
     */
    private function generateTokenData(int $uid): array
    {
        /**
         * iss：jwt签发者
         * sub：jwt所面向的用户
         * aud：接收jwt的一方
         * exp：jwt的过期时间，这个过期时间必须要大于签发时间
         * nbf：定义在什么时间之前，该jwt都是不可用的
         * iat：jwt的签发时间
         * jti：jwt的唯一身份标识，主要用来作为一次性token，从而回避重放攻击
         */
        $time    = time();
        $request = getRequest();
        return [
            'iss' => 'system',
            'sub' => 'all',
            'aud' => $request->host(),
            'exp' => $time + 3600,
            'nbf' => $time,
            'iat' => $time,
            'jti' => $request->getRequestId(),
            'uid' => $uid
        ];
    }

    /**
     * 获取token
     *
     * @return string
     */
    private function getToken(): string
    {
        return getRequest()->header('token', '');
    }

    /**
     * 解析token
     *
     * @param string $token
     * @return int
     */
    private function parseToken(string $token): int
    {
        try {
            $data = $this->decryptRSA($token);
            if (empty($data) || empty($data['nbf']) || empty($data['exp']) || empty($data['uid'])) {
                throw new UnauthorizedException();
            }

            $time = time();
            $host = getRequest()->host();
            if ($time < $data['nbf'] || $time > $data['exp'] || $host != $data['aud']) {
                throw new UnauthorizedException();
            }

            return $data['uid'];
        } catch (Throwable) {
            throw new UnauthorizedException();
        }
    }

    /**
     * 检测是否是超级管理员
     *
     * @param int $uid
     * @return bool
     */
    private function isSuperAdmin(int $uid): bool
    {
        return in_array($uid, config('api.super_admin_list'));
    }
}