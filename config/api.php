<?php
// +----------------------------------------------------------------------
// | API设置
// +----------------------------------------------------------------------
return [
    // API返回数据格式
    'return_data_type' => 'json',
    // 超级管理员列表
    'super_admin_list' => [1, 12],
    // API免登录名单
    'exempt_login'     => [
        'Common/login',
        'Common/register',
        'Oauth/authorize',
        'Oauth/token',
        'Store/linkStore'
    ],
    // API白名单
    'white_list'       => [
        'Common/logout',
        'Oauth/authorize'
    ]
];