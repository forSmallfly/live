<?php

namespace app\server\oauth\entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use ClientTrait;
    use EntityTrait;

    public function __construct($identifier, $name, $redirectUri, $isConfidential)
    {
        $this->identifier     = $identifier;
        $this->name           = $name;
        $this->redirectUri    = $redirectUri;
        $this->isConfidential = $isConfidential;
    }
}