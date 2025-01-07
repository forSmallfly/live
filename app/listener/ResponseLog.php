<?php
declare (strict_types = 1);

namespace app\listener;

use Psr\Log\LogLevel;
use think\facade\Log;
use think\Response;

class ResponseLog
{
    /**
     * 添加响应日志
     *
     * @param Response $response
     * @return void
     */
    public function handle(Response $response): void
    {
        if (app()->isDebug()) {
            $msg = "[{requestId}]\nreturn：{return}\n{separator}";

            if (isset($response->getData()['data']['list']) && count($response->getData()['data']['list']) > 1) {
                $return = '数据过多，不予记录！';
            } else {
                $return = var_export($response->getData(), true);
            }

            Log::record($msg, LogLevel::INFO, [
                'requestId' => getRequest()->getRequestId(),
                'return'    => $return,
                'separator' => '========================================================================================================================'
            ]);
        }
    }
}
