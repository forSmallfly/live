<?php

namespace app\server\oauth\repository;

use app\model\OauthClientScopes;
use app\model\OauthScopes;
use app\server\oauth\entity\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * Return information about a scope.
     *
     * @param $identifier
     * @return ScopeEntity|null
     */
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntity
    {
        $scopeInfo = OauthScopes::getInstance()->where([
            ['scope', '=', $identifier]
        ])->findOrEmpty();

        if (empty($scopeInfo)) {
            return null;
        }

        $scopeEntity = new ScopeEntity();
        $scopeEntity->setIdentifier($scopeInfo['scope']);
        return $scopeEntity;
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param array $scopes
     * @param $grantType
     * @param ClientEntityInterface $clientEntity
     * @param $userIdentifier
     * @return array|ScopeEntityInterface[]
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
    {
        $allowedScopes = $this->getAllowedScopesForClient($clientEntity->getIdentifier());
        $defaultScopes = $this->getDefaultScopes();

        $finalScopes = [];
        foreach ($scopes as $scope) {
            if (in_array($scope->getIdentifier(), $allowedScopes)) {
                $finalScopes[] = $scope;
            }
        }

        if (empty($finalScopes)) {
            foreach ($defaultScopes as $defaultScope) {
                $scopeEntity = $this->getScopeEntityByIdentifier($defaultScope);
                if ($scopeEntity) {
                    $finalScopes[] = $scopeEntity;
                }
            }
        }

        return $finalScopes;
    }

    /**
     * 获取客户允许使用的权限范围
     *
     * @param string $clientId
     * @return array
     */
    private function getAllowedScopesForClient(string $clientId): array
    {
        return OauthClientScopes::getInstance()->where([
            ['client_id', '=', $clientId]
        ])->column('scope');
    }

    /**
     * 获取默认的权限范围
     *
     * @return string[]
     */
    private function getDefaultScopes(): array
    {
        return ['basic'];
    }
}