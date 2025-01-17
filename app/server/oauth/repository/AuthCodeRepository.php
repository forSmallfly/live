<?php

namespace app\server\oauth\repository;

use app\model\OauthAuthorizationCodes;
use app\server\oauth\entity\AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use think\db\exception\DbException;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * Creates a new AuthCode
     *
     * @return AuthCodeEntity
     */
    public function getNewAuthCode(): AuthCodeEntity
    {
        return new AuthCodeEntity();
    }

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     * @return void
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $data = [
            'code'       => $authCodeEntity->getIdentifier(),
            'user_id'    => $authCodeEntity->getUserIdentifier(),
            'client_id'  => $authCodeEntity->getClient()->getIdentifier(),
            'scopes'     => json_encode($authCodeEntity->getScopes()),
            'expires_at' => $authCodeEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
        ];

        OauthAuthorizationCodes::getInstance()->insert($data);
    }

    /**
     * Revoke an auth code.
     *
     * @param $codeId
     * @return void
     */
    public function revokeAuthCode($codeId): void
    {
        OauthAuthorizationCodes::getInstance()->where([
            ['code', '=', $codeId]
        ])->update([
            'revoked' => 1
        ]);
    }

    /**
     * Check if the auth code has been revoked.
     *
     * @param $codeId
     * @return bool
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        $info = OauthAuthorizationCodes::getInstance()
            ->field('revoked')
            ->where([
                ['code', '=', $codeId]
            ])->findOrEmpty();

        return !empty($info) && $info['revoked'];
    }
}