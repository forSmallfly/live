<?php

namespace Feature\controller;

use app\controller\Index;
use HttpCase;

class IndexTest extends HttpCase
{
    public function testHello()
    {
        /*** @see Index::hello() */
        $response = $this->get('/hello/world');

        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('hello,world', $response->getContent());
    }

    public function testThink()
    {
        $response = $this->get('/think');

        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('hello,ThinkPHP8!', $response->getContent());
    }
}