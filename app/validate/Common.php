<?php
declare (strict_types=1);

namespace app\validate;

use app\BaseValidate;

class Common extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'account'  => ['require', 'max' => 50],
        'password' => ['require', 'max' => 250],
        'mobile'   => ['require', 'mobile'],
        'email'    => ['require', 'email']
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'account.require'  => '用户名必须',
        'account.max'      => '用户名长度最大为50',
        'password.require' => '密码必须',
        'password.max'     => '密码长度最大为250',
        'mobile.require'   => '手机号必须',
        'mobile.mobile'    => '手机号格式不正确',
        'email.require'    => '邮箱必须',
        'email.email'      => '邮箱格式不正确'
    ];

    /**
     * 定义验证场景
     * 格式：'场景名' =>  ['字段名1','字段名2'...]
     *
     * @var array[]
     */
    protected $scene = [
        'login'    => ['account', 'password'],
        'logout'   => [''],
        'register' => ['account', 'password', 'mobile', 'email']
    ];
}
