<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
use app\command\Canal;
use app\command\Kafka;

return [
    // 指令定义
    'commands' => [
        'canal' => Canal::class,
        'kafka' => Kafka::class,
    ],
];
