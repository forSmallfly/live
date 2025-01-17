<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\server\oauth\OauthServer;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use think\annotation\route\Route;
use think\response\Json;

class Oauth extends BaseController
{
    /**
     * @return \think\Response
     */
    #[Route("GET", "oauth/authorize")]
    public function authorize(): \think\Response
    {
        $server = new OauthServer();

        $request  = ServerRequestFactory::fromGlobals(
            $_SERVER,             // 服务器和执行环境相关信息
            getRequest()->get(),  // GET 请求参数
            getRequest()->post(), // POST 请求参数
            $_COOKIE,             // COOKIE 数据
            $_FILES               // 文件上传数据
        );
        $response = new Response();
        $result   = $server->authorize($request, $response);

        $url = $result->getHeader('Location')[0];

        return $this->success([
            'url' => $url
        ]);
    }

    /**
     * @return \think\Response
     */
    #[Route("POST", "oauth/token")]
    public function token(): \think\Response
    {
        $server = new OauthServer();

        $request  = ServerRequestFactory::fromGlobals(
            $_SERVER,             // 服务器和执行环境相关信息
            getRequest()->get(),  // GET 请求参数
            getRequest()->post(), // POST 请求参数
            $_COOKIE,             // COOKIE 数据
            $_FILES               // 文件上传数据
        );
        $response = new Response();
        $data     = $server->token($request, $response)->getBody()->__toString();

        $data = json_decode($data, true);
        return $this->success($data);
    }
}
