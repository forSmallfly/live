<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;
use think\model\concern\SoftDelete;

class Role extends BaseModel
{
    use SoftDelete;

    /**
     * 获取角色拥有的权限ID列表
     *
     * @param int|array $roleId
     * @return array
     */
    public function getRuleIdList(int|array $roleId): array
    {
        $formula = is_array($roleId) ? 'in' : '=';
        $where   = [
            ['id', $formula, $roleId]
        ];

        $list = $this->fieldList('rules', $where);
        if (empty($list)) {
            return [];
        }

        $ruleIdList = [];
        foreach ($list as $rules) {
            if (empty($rules)) {
                continue;
            }

            $rules      = explode(',', $rules);
            $ruleIdList = array_merge($ruleIdList, $rules);
        }

        return $ruleIdList;
    }
}
