<?php
declare (strict_types = 1);

namespace app\command;

use app\server\CanalServer;
use app\server\KafkaServer;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Canal extends Command
{
    protected function configure(): void
    {
        // 指令配置
        $this->setName('canal')
            ->setDescription('the canal command');
    }

    protected function execute(Input $input, Output $output): void
    {
        $canalServer = new CanalServer();
        $canalServer->pull(function (array $data) use ($output) {
            $kafkaServer = new KafkaServer();
            $result      = $kafkaServer->push('test', json_encode($data));

            // 指令输出
            $output->writeln((string)$result);
        });
    }
}
