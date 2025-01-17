<?php

namespace app;

// 应用请求对象类
class Request extends \think\Request
{
    /**
     * 兼容PATH_INFO获取
     * @var array
     */
    protected $pathinfoFetch = ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL', 'REQUEST_URI'];

    /**
     * 用户ID
     *
     * @var int
     */
    private int $uid = 0;

    /**
     * 请求ID
     *
     * @var string
     */
    private string $requestId = '';

    /**
     * 请求参数
     *
     * @var array
     */
    private array $params = [];

    /**
     * 获取请求ID
     *
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * 设置请求ID
     *
     * @param string $requestId
     * @return void
     */
    public function setRequestId(string $requestId = ''): void
    {
        $chars = md5(uniqid((string)mt_rand(), true));

        $requestId = $requestId ?: substr($chars, 0, 8) . '-'
            . substr($chars, 8, 4) . '-'
            . substr($chars, 12, 4) . '-'
            . substr($chars, 16, 4) . '-'
            . substr($chars, 20, 12);

        $this->requestId = strtoupper($requestId);
    }

    /**
     * 获取请求参数
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * 设置请求参数
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * 获取用户ID
     *
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * 设置用户ID
     *
     * @param int $uid
     * @return void
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }
}
