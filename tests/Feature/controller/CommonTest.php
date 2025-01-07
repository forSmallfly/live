<?php

namespace Feature\controller;

use app\controller\Common;
use HttpCase;

class CommonTest extends HttpCase
{
    /**
     * @return string[]
     */
    public function testRegister()
    {
        $params = [
            'account'  => 'test' . uniqid(),
            'password' => 'test' . uniqid(),
            'mobile'   => '131' . rand(10000000, 99999999),
            'email'    => '131' . rand(10000000, 99999999) . '@qq.com'
        ];

        /*** @see Common::register() */
        $response = $this->post('/common/register', $params);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);

        return $params;
    }

    /**
     * @depends testRegister
     *
     * @return string
     */
    public function testLogin(array $params)
    {
        /*** @see Common::login() */
        $response = $this->post('/common/login', [
            'account'  => $params['account'],
            'password' => $params['password']
        ]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);
        return $data['data']['token'];
    }

    /**
     * @depends testLogin
     *
     * @return void
     */
    public function testLogout(string $token)
    {
        /*** @see Common::logout() */
        $response = $this->post('/common/logout', [], ['token' => $token]);

        $data = $response->getData();
        $this->assertEquals('success', $data['msg']);
    }
}
