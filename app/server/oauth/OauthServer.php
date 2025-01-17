<?php

namespace app\server\oauth;

use app\server\oauth\entity\UserEntity;
use app\server\oauth\repository\AccessTokenRepository;
use app\server\oauth\repository\AuthCodeRepository;
use app\server\oauth\repository\ClientRepository;
use app\server\oauth\repository\RefreshTokenRepository;
use app\server\oauth\repository\ScopeRepository;
use DateInterval;
use Exception;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OauthServer
{
    private AuthorizationServer $server;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        // Init our repositories
        $clientRepository       = new ClientRepository();
        $scopeRepository        = new ScopeRepository();
        $accessTokenRepository  = new AccessTokenRepository();
        $authCodeRepository     = new AuthCodeRepository();
        $refreshTokenRepository = new RefreshTokenRepository();

        $keyPath       = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . 'private.key';
        $privateKey    = new CryptKey($keyPath, null, false);
        $encryptionKey = 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'; // generate using base64_encode(random_bytes(32))

        // Setup the authorization server
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );

        $grant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new DateInterval('PT10M') // authorization codes will expire after 10 minutes
        );

        $grant->setRefreshTokenTTL(new DateInterval('P1M')); // refresh tokens will expire after 1 month

        // Enable the authentication code grant on the server
        $server->enableGrantType(
            $grant,
            new DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        $this->server = $server;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface|void
     */
    public function authorize(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            // Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $this->server->validateAuthorizationRequest($request);
            // The auth request object can be serialized and saved into a user's session.
            // You will probably want to redirect the user at this point to a login endpoint.

            $userEntity = new UserEntity();
            // TODO 这里需要获取当前登录用户的ID
            $userEntity->setIdentifier($request->getQueryParams()['client_id']);
            // Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser($userEntity); // an instance of UserEntityInterface

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            return $this->server->completeAuthorizationRequest($authRequest, $response);

        } catch (OAuthServerException|Exception $exception) {
            echo var_export([
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'msg'  => $exception->getMessage()
            ], true);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface|void
     */
    public function token(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            // 处理请求并生成响应
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // 处理OAuth错误
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            echo var_export([
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'msg'  => $exception->getMessage()
            ], true);
        }
    }
}