<?php

namespace app\utils;

use think\Response;

/**
 * 响应工具库
 */
trait ResponseTool
{
    /**
     * 成功返回
     *
     * @param array $data
     * @param int $code
     * @param string $msg
     * @return Response
     */
    protected final function success(array $data = [], int $code = 0, string $msg = 'success'): Response
    {
        $result = [
            'id'   => getRequest()->getRequestId(),
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        $type = $this->getReturnDataType();
        return match ($type) {
            'xml'   => xml($result),
            default => json($result),
        };
    }

    /**
     * 失败返回
     *
     * @param int $code
     * @param string $msg
     * @return Response
     */
    protected final function fail(int $code = -1, string $msg = 'fail'): Response
    {
        $result = [
            'id'   => getRequest()->getRequestId(),
            'code' => $code,
            'msg'  => $msg
        ];

        $type = $this->getReturnDataType();
        return match ($type) {
            'xml'   => xml($result),
            default => json($result),
        };
    }

    /**
     * 获取API返回数据格式
     *
     * @return string
     */
    private function getReturnDataType(): string
    {
        return config('api.return_data_type', 'json');
    }
}