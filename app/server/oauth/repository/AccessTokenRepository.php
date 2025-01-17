<?php

namespace app\server\oauth\repository;

use app\model\OauthAccessTokens;
use app\server\oauth\entity\AccessTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * Create a new access token
     *
     * @param ClientEntityInterface $clientEntity
     * @param array $scopes
     * @param $userIdentifier
     * @return AccessTokenEntity
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntity
    {
        $accessTokenEntity = new AccessTokenEntity();
        $accessTokenEntity->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessTokenEntity->addScope($scope);
        }
        $accessTokenEntity->setUserIdentifier($userIdentifier);
        return $accessTokenEntity;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @return void
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $scopes = implode(' ', array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $accessTokenEntity->getScopes()));

        $data = [
            'access_token' => $accessTokenEntity->getIdentifier(),
            'user_id'      => $accessTokenEntity->getUserIdentifier(),
            'client_id'    => $accessTokenEntity->getClient()->getIdentifier(),
            'scopes'       => $scopes,
            'revoked'      => 0,
            'expires_at'   => $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
        ];

        OauthAccessTokens::getInstance()->insert($data);
    }

    /**
     * Revoke an access token.
     *
     * @param $tokenId
     * @return void
     */
    public function revokeAccessToken($tokenId): void
    {
        OauthAccessTokens::getInstance()->where([
            ['access_token', '=', $tokenId]
        ])->update([
            'revoked' => 1
        ]);
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param $tokenId
     * @return bool
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        $info = OauthAccessTokens::getInstance()
            ->field('revoked')
            ->where([
                ['access_token', '=', $tokenId]
            ])->findOrEmpty();
        if (empty($info)) {
            return true;
        }

        return (bool)$info['revoked'];
    }
}