<?php

namespace Feature\controller;

use app\controller\Demo;
use HttpCase;

class DemoTest extends HttpCase
{
    /**
     * 获取资源列表
     *
     * @return void
     */
    public function testGet()
    {
        /*** @see Demo::list() */
        $response = $this->get('/demo/list?page=1&list_rows=10', [], ['token' => $this->token]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);
    }

    /**
     * 获取资源信息
     *
     * @return void
     */
    public function testInfo()
    {
        /*** @see Demo::info() */
        $response = $this->get('/demo/info?id=1', [], ['token' => $this->token]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);
    }

    /**
     * 新建资源
     *
     * @return void
     */
    public function testPost()
    {
        /*** @see Demo::add() */
        $response = $this->post('/demo/add', ['name' => uniqid()], ['token' => $this->token]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);

        return $data['data']['id'];
    }

    /**
     * 更新资源
     * @depends testPost
     *
     * @return void
     */
    public function testPut(int $id)
    {
        /*** @see Demo::update() */
        $response = $this->put('/demo/update', [
            'id'   => $id,
            'name' => uniqid()
        ], [
            'token'        => $this->token,
            'Content-Type' => 'application/json; charset=utf-8'
        ]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);
    }

    /**
     * 删除资源
     * @depends testPost
     *
     * @return void
     */
    public function testDelete(int $id)
    {
        /*** @see Demo::delete() */
        $response = $this->delete('/demo/delete', [
            'id' => $id
        ], [
            'token'        => $this->token,
            'Content-Type' => 'application/json; charset=utf-8'
        ]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);
    }
}