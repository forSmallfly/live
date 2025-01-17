<?php
// 事件定义文件
use app\listener\RequestLog;
use app\listener\ResponseLog;

return [
    'bind'      => [
    ],
    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [
            RequestLog::class,
        ],
        'HttpEnd'  => [
            ResponseLog::class,
        ],
        'LogLevel' => [],
        'LogWrite' => [],
    ],
    'subscribe' => [
    ],
];
