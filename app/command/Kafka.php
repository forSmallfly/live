<?php
declare (strict_types = 1);

namespace app\command;

use app\server\KafkaServer;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Kafka extends Command
{
    protected function configure(): void
    {
        // 指令配置
        $this->setName('kafka')
            ->setDescription('the kafka command');
    }

    protected function execute(Input $input, Output $output): void
    {
        $kafkaServer = new KafkaServer();
        $kafkaServer->pull(['test'], function (string $payload) use ($output) {
            trace($payload);

            // 指令输出
            $output->writeln($payload);

            return true;
        });
    }
}
