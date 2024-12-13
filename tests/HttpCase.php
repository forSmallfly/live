<?php

use PHPUnit\Framework\TestCase;
use think\App;
use think\Response;

class HttpCase extends TestCase
{
    /**
     * @var App
     */
    public App $app;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->app = new App();
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

        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->setMethod('get')
            ->withServer(['REQUEST_URI' => $url])
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
        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->setMethod('post')
            ->withServer(['REQUEST_URI' => $url])
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
        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->withHeader($header)
            ->setMethod('put')
            ->withServer(['REQUEST_URI' => $url])
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
        // 每一次都创建新的request对象，防止同一次单元测试中多个请求相互影响
        $request = $this->app->make('request', [], true)
            ->withHeader($header)
            ->setMethod('delete')
            ->withServer(['REQUEST_URI' => $url])
            ->withInput(json_encode($data));

        $response = $this->app->http->run($request);
        $this->app->http->end($response);

        return $response;
    }
}