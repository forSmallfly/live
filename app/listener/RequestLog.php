<?php
declare (strict_types = 1);

namespace app\listener;

use Psr\Log\LogLevel;
use think\facade\Log;

class RequestLog
{
    /**
     * 添加请求日志
     *
     * @return void
     */
    public function handle(): void
    {
        $request = getRequest();
        // 设置请求ID
        $request->setRequestId();

        if (app()->isDebug()) {
            $msg = "[{requestId}] {method} {scheme}://{host}{url}\nheader：{header}\nparam：{data}";

            $method = strtolower($request->method());
            Log::record($msg, LogLevel::INFO, [
                'requestId' => getRequest()->getRequestId(),
                'method'    => $request->method(),
                'scheme'    => $request->scheme(),
                'host'      => $request->host() ?: 'localhost',
                'url'       => $request->url(),
                'header'    => var_export($request->header(), true),
                'data'      => var_export($request->$method(), true)
            ]);
        }
    }
}
