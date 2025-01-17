<?php

namespace app\server;

use Closure;
use Exception;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;

class KafkaServer
{
    private Conf $conf;

    /**
     * @param string $broker
     */
    public function __construct(string $broker = '127.0.0.1:9092')
    {
        // 引入 rdkafka
        $conf = new Conf();
        // 设置 Kafka 代理服务器地址
        $conf->set('metadata.broker.list', $broker);
        // 设置配置到全局
        $this->conf = $conf;
    }

    /**
     * @param string $topicName
     * @param string $payload
     * @return int
     */
    public function push(string $topicName, string $payload): int
    {
        $conf = $this->conf;
        // 设置确认机制为 all
        // acks=0：生产者不会等待任何确认消息，立即返回。
        // acks=1：生产者等待 Kafka leader 分区的确认，leader 成功写入日志后返回确认。
        // acks=all（或 acks=-1）：生产者等待所有副本的确认，确保消息在所有副本成功写入后才返回确认。
        $conf->set("acks", "all");
        // 创建生产者实例
        $producer = new Producer($conf);
        // 创建一个 Kafka 主题
        $topic = $producer->newTopic($topicName);
        // 发送消息
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload);
        // 让生产者进行消息发送操作
        $producer->poll(0);
        // 等待生产者的所有消息发送完成
        return $producer->flush(10000);
    }

    /**
     * @param array $topics
     * @param Closure $callback
     * @return mixed
     */
    public function pull(array $topics, Closure $callback): mixed
    {
        $conf = $this->conf;
        // 配置消费者组
        $conf->set('group.id', 'group1');
        // 禁用自动提交
        $conf->set('enable.auto.commit', 'false');
        // 设置当前消费者拉取数据时的偏移量， 可选参数：
        // earliest: 如果消费者组是新创建的，从头开始消费，否则从消费者组当前消费位移开始。
        // latest:如果消费者组是新创建的，从最新偏移量开始，否则从消费者组当前消费位移开始。
        $conf->set('auto.offset.reset', 'earliest');
        // 创建消费者实例
        $consumer = new KafkaConsumer($conf);
        // 订阅主题
        $consumer->subscribe($topics);

        while (true) {
            $message = $consumer->consume(120 * 1000); // 消息超时时间为 120 秒
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $result = $callback($message->payload);
                    if ($result) {
                        // 处理完消息后手动提交 Offset
                        $consumer->commit();
                    }
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo '已到达分区结尾';
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo '超时';
                    break;
                default:
                    echo $message->errstr();
                    break;
            }
        }
    }

    /**
     * @return array
     */
    public function getTopics(): array
    {
        $conf = $this->conf;
        // 添加 group.id 配置
        $conf->set('group.id', 'testMeta'); // 消费者组 ID，需自定义
        // 创建消费者实例
        $consumer = new KafkaConsumer($conf);

        // 获取 Kafka 元数据（包括主题列表）
        try {
            $topics   = [];
            $metadata = $consumer->getMetadata(true, null, 60000);// 超时时间：60秒
            foreach ($metadata->getTopics() as $topic) {
                $topics[] = $topic->getTopic();
            }

            return $topics;
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return [];
    }
}