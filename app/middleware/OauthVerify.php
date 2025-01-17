<?php
declare (strict_types = 1);

namespace app\middleware;

use app\Request;
use app\server\oauth\repository\AccessTokenRepository;
use Closure;
use Laminas\Diactoros\ServerRequestFactory;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use think\Response;

class OauthVerify
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws OAuthServerException
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Init our repositories
        $accessTokenRepository = new AccessTokenRepository(); // instance of AccessTokenRepositoryInterface

        // Path to authorization server's public key
        $keyPath       = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . 'public.key';
        $publicKeyPath = new CryptKey($keyPath, null, false);
        // Setup the authorization server
        $server = new ResourceServer(
            $accessTokenRepository,
            $publicKeyPath
        );

        $ServerRequest = ServerRequestFactory::fromGlobals(
            $request->server(),   // 服务器和执行环境相关信息
            $request->get(),      // GET 请求参数
            $request->post(),     // POST 请求参数
            $_COOKIE,             // COOKIE 数据
            $_FILES               // 文件上传数据
        );

        // 使用 ResourceServer 验证请求中的访问令牌
        $ServerRequest = $server->validateAuthenticatedRequest($ServerRequest);

        // 如果验证成功，你可以继续处理这个请求
        // 验证后的请求对象将包含访问令牌的相关信息

        $token    = $ServerRequest->getAttribute('oauth_access_token_id'); // 获取 token ID
        $userId   = $ServerRequest->getAttribute('oauth_user_id');         // 获取 user ID
        $clientId = $ServerRequest->getAttribute('oauth_client_id');       // 获取 client ID
        $scopes   = $ServerRequest->getAttribute('oauth_scopes');          // 获取授权范围

        return $next($request);
    }
}
