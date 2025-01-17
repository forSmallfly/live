<?php

use app\utils\RequestTool;
use PHPUnit\Framework\TestCase;
use think\App;
use think\Response;

class HttpCase extends TestCase
{
    use RequestTool;

    /**
     * @var App
     */
    public App $app;

    /**
     * @var string
     */
    protected string $token = '';

    /**
     * 登录参数
     *
     * @var array|string[]
     */
    protected array $loginParams = [
        'account'  => 'admin',
        'password' => 'admin'
    ];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->app = new App();
        if (empty($this->token)) {
            $this->login($this->loginParams);
        }
    }

    /**
     * @depends testRegister
     *
     * @param array $params
     * @return void
     */
    public function login(array $params): void
    {
        /*** @see Common::login() */
        $response = $this->post('/common/login', [
            'account'  => $params['account'],
            'password' => $params['password']
        ]);

        $data = $response->getData();

        $this->token        = $data['data']['token'];
        $this->app->request = null;
    }

    /**
     * 模拟get请求
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return Response
     */
    protected function get(string $url, array $data = [], array $header = []): Response
    {
        // 解析链接中的参数
        $urlInfo = parse_url($url);
        if (!empty($urlInfo['query'])) {
            $queryList = explode('&', $urlInfo['query']);
            foreach ($queryList as $queryInfo) {
                [$field, $value] = explode('=', $queryInfo);
                $data[$field] = $value;
            }
        }

        $server = ['REQUEST_URI' => $url];
        foreach ($header as $name => $value) {
            $server['http_' . $name] = $value;
        }

        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->setMethod('get')
            ->withServer($server)
            ->withGet($data)
            ->withHeader($header);

        $response = $this->app->http->run($request);
        $this->app->http->end($response);

        return $response;
    }

    /**
     * 模拟post请求
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return Response
     */
    protected function post(string $url, array $data = [], array $header = []): Response
    {
        $server = ['REQUEST_URI' => $url];
        foreach ($header as $name => $value) {
            $server['http_' . $name] = $value;
        }

        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->setMethod('post')
            ->withServer($server)
            ->withPost($data)
            ->withHeader($header);

        $response = $this->app->http->run($request);
        $this->app->http->end($response);

        return $response;
    }

    /**
     * 模拟put请求
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return Response
     */
    protected function put(string $url, array $data = [], array $header = []): Response
    {
        $server = ['REQUEST_URI' => $url];
        foreach ($header as $name => $value) {
            $server['http_' . $name] = $value;
        }

        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->withHeader($header)
            ->setMethod('put')
            ->withServer($server)
            ->withInput(json_encode($data));

        $response = $this->app->http->run($request);
        $this->app->http->end($response);

        return $response;
    }

    /**
     * 模拟delete请求
     *
     * @param string $url
     * @param array $data
     * @param array $header
     * @return Response
     */
    protected function delete(string $url, array $data = [], array $header = []): Response
    {
        $server = ['REQUEST_URI' => $url];
        foreach ($header as $name => $value) {
            $server['http_' . $name] = $value;
        }

        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->withHeader($header)
            ->setMethod('delete')
            ->withServer($server)
            ->withInput(json_encode($data));

        $response = $this->app->http->run($request);
        $this->app->http->end($response);

        return $response;
    }
}