<?php
declare (strict_types=1);

namespace app\validate;

use app\BaseValidate;

class Demo extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'        => ['require', 'integer', 'max' => 11],
        'name'      => ['require', 'max' => 30],
        'page'      => ['integer', 'max' => 11],
        'list_rows' => ['integer', 'max' => 11]
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'id.require'        => '数据标识必须',
        'id.integer'        => '数据标识数据格式不正确',
        'id.max'            => '数据标识长度最大为11',
        'name.require'      => '测试名称必须',
        'name.max'          => '测试名称长度最大为30',
        'page.integer'      => '第几页数据格式不正确',
        'page.max'          => '第几页长度最大为11',
        'list_rows.integer' => '每页数据条数数据格式不正确',
        'list_rows.max'     => '每页数据条数长度最大为11'
    ];

    /**
     * 定义验证场景
     * 格式：'场景名' =>  ['字段名1','字段名2'...]
     *
     * @var array[]
     */
    protected $scene = [
        'info'   => ['id'],
        'add'    => ['name'],
        'update' => ['id', 'name'],
        'delete' => ['id']
    ];

    /**
     * list 验证场景定义
     *
     * @return Demo
     */
    public function sceneList(): Demo
    {
        return $this->remove('id', true)
            ->remove('name', 'require');
    }
}
