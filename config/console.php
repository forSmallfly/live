<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
use app\command\Canal;
use app\command\Kafka;
use app\command\make\Item;

return [
    // 指令定义
    'commands' => [
        'canal'     => Canal::class,
        'kafka'     => Kafka::class,
        'make:item' => Item::class,
    ],
];
