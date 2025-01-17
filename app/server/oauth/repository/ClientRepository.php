<?php

namespace app\server\oauth\repository;

use app\model\OauthClients;
use app\server\oauth\entity\ClientEntity;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Get a client.
     *
     * @param $clientIdentifier
     * @return ClientEntity|null
     */
    public function getClientEntity($clientIdentifier): ?ClientEntity
    {
        $clientData = OauthClients::getInstance()->where([
            ['identifier', '=', $clientIdentifier]
        ])->findOrEmpty();

        if ($clientData) {
            return new ClientEntity($clientData['identifier'], $clientData['name'], $clientData['redirect_uri'], true);
        }

        return null;
    }

    /**
     * Validate a client's secret.
     *
     * @param $clientIdentifier
     * @param $clientSecret
     * @param $grantType
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $clientData = OauthClients::getInstance()
            ->field('id')
            ->where([
                ['identifier', '=', $clientIdentifier],
                ['secret', '=', $clientSecret],
            ])
            ->whereFindInSet('grant_types', $grantType)
            ->findOrEmpty();

        return (bool)$clientData;
    }
}