<?php

namespace app\server;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

class ElasticSearchServer
{
    private Client $client;

    public function __construct()
    {
        // 初始化 Elasticsearch 客户端
        try {
            $client = ClientBuilder::create()->setHosts(['http://localhost:9200'])->build();
            // 测试连接
            try {
                $response     = $client->info();
                $this->client = $client;
            } catch (ClientResponseException|ServerResponseException $e) {
                echo $e->getMessage();
            }
        } catch (AuthenticationException $e) {
            echo $e->getMessage();
        }
    }

    public function insert(): bool|Elasticsearch|Promise
    {
        $params = [
            'index' => 'test_index',
            'id'    => '1', // 文档ID
            'body'  => [
                'name' => 'John Doe',
                'age'  => 30
            ]
        ];

        try {
            return $this->client->index($params);
        } catch (ClientResponseException|MissingParameterException|ServerResponseException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function query(): bool|Elasticsearch|Promise
    {
        $params = [
            'index' => 'test_index',
            'body'  => [
                'query' => [
                    'match' => [
                        'name' => 'John'
                    ]
                ]
            ]
        ];

        try {
            return $this->client->search($params);
        } catch (ClientResponseException|ServerResponseException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function search(): bool|Elasticsearch|Promise
    {
        $params = [
            'index' => 'test_index',
            'body'  => [
                'aggs' => [
                    'avg_age' => [
                        'avg' => [
                            'field' => 'age'
                        ]
                    ]
                ]
            ]
        ];

        try {
            return $this->client->search($params);
        } catch (ClientResponseException|ServerResponseException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function update(): bool|Elasticsearch|Promise
    {
        $params = [
            'index' => 'test_index',
            'id'    => '1',
            'body'  => [
                'doc' => [
                    'age' => 31
                ]
            ]
        ];

        try {
            return $this->client->update($params);
        } catch (ClientResponseException|ServerResponseException|MissingParameterException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function delete(): bool|Elasticsearch|Promise
    {
        $params = [
            'index' => 'test_index',
            'id'    => '1'
        ];

        try {
            return $this->client->delete($params);
        } catch (ClientResponseException|ServerResponseException|MissingParameterException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}