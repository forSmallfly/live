<?php

namespace app\server\oauth\repository;

use app\model\OauthRefreshTokens;
use app\server\oauth\entity\RefreshTokenEntity;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use think\db\exception\DbException;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * Creates a new refresh token
     *
     * @return RefreshTokenEntity
     */
    public function getNewRefreshToken(): RefreshTokenEntity
    {
        return new RefreshTokenEntity();
    }

    /**
     * Create a new refresh token_name.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     * @return void
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $data = [
            'refresh_token' => $refreshTokenEntity->getIdentifier(),
            'access_token'  => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'expires_at'    => $refreshTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'revoked'       => 0,
        ];
        OauthRefreshTokens::getInstance()->insert($data);
    }

    /**
     * Revoke the refresh token.
     *
     * @param $tokenId
     * @return void
     */
    public function revokeRefreshToken($tokenId): void
    {
        OauthRefreshTokens::getInstance()->where([
            ['refresh_token', '=', $tokenId]
        ])->update([
            'revoked' => 1
        ]);
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param $tokenId
     * @return bool
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        $info = OauthRefreshTokens::getInstance()
            ->field('revoked')
            ->where([
                ['refresh_token', '=', $tokenId]
            ])->findOrEmpty();

        if (empty($info)) {
            return true;
        }

        return (bool)$info['revoked'];
    }
}