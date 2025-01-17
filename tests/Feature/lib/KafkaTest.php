<?php

namespace Feature\lib;

use app\server\KafkaServer;
use HttpCase;

class KafkaTest extends HttpCase
{
    public function testMeta()
    {
        $kafkaServer = new KafkaServer();

        $topics = $kafkaServer->getTopics();
        $this->assertNotEmpty($topics);
    }
}