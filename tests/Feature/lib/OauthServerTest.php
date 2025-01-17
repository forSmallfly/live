<?php

namespace Feature\lib;

use app\controller\Oauth;
use app\controller\Store;
use HttpCase;

class OauthServerTest extends HttpCase
{
    /**
     * @var string
     */
    private string $clientId = 'shippingID';

    /**
     * @var string
     */
    private string $clientSecret = 'shippingSecret';

    /**
     * @var string
     */
    private string $redirectUri = 'http://your-client.com/callback';

    /**
     * @var string
     */
    private string $scope = 'link_shop unlink_shop';

    /**
     * @var string
     */
    private string $state = 'state';

    /**
     * @return mixed|null
     */
    public function testAuthorize()
    {
        /*** @see Oauth::authorize() */
        $response = $this->get('/oauth/authorize', [
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'scope'         => $this->scope,
            'state'         => $this->state,
        ]);

        $url = $response->getData()['data']['url'];
        $this->assertIsString($url);

        $urlParts = parse_url($url);

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $queryParams);

            $this->assertNotEmpty($queryParams['code']);
            return $queryParams['code'];
        } else {
            return null;
        }
    }

    /**
     * @depends testAuthorize
     *
     * @return void
     */
    public function testToken($code)
    {
        /*** @see Oauth::token() */
        $response = $this->post('/oauth/token', [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->redirectUri,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        $data = $response->getData();

        $this->assertNotEmpty($data['data']['access_token']);
        $this->assertIsString($data['data']['access_token']);

        return $data['data']['access_token'];
    }

    /**
     * @depends testToken
     *
     * @return void
     */
    public function testLinkStore($accessToken)
    {
        /*** @see Store::linkStore() */
        $response = $this->post('store/link_store', [], [
            'authorization' => $accessToken
        ]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);
    }
}