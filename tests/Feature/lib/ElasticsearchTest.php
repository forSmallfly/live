<?php

namespace Feature\lib;

use app\server\ElasticSearchServer;
use HttpCase;

class ElasticsearchTest extends HttpCase
{
    public function testElasticsearch()
    {
        $elasticsearchServer = new ElasticSearchServer();

        $this->assertNotEmpty($elasticsearchServer);
    }

    public function testInsert()
    {
        $elasticsearchServer = new ElasticSearchServer();

        $response = $elasticsearchServer->insert();
        $this->assertNotFalse($response);
        $this->assertNotEmpty($response->asArray());

        print_r($response->asArray());
    }

    public function testQuery()
    {
        $elasticsearchServer = new ElasticSearchServer();

        $response = $elasticsearchServer->query();
        $this->assertNotFalse($response);
        $this->assertNotEmpty($response->asArray());

        print_r($response->asArray());
    }

    public function testSearch()
    {
        $elasticsearchServer = new ElasticSearchServer();

        $response = $elasticsearchServer->search();
        $this->assertNotFalse($response);
        $this->assertNotEmpty($response->asArray());

        print_r($response->asArray());
    }

    public function testUpdate()
    {
        $elasticsearchServer = new ElasticSearchServer();

        $response = $elasticsearchServer->update();
        $this->assertNotFalse($response);
        $this->assertNotEmpty($response->asArray());

        print_r($response->asArray());
    }

    public function testDelete()
    {
        $elasticsearchServer = new ElasticSearchServer();

        $response = $elasticsearchServer->delete();
        $this->assertNotFalse($response);
        $this->assertNotEmpty($response->asArray());

        print_r($response->asArray());
    }
}