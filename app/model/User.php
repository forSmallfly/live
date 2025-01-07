<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;
use think\model\concern\SoftDelete;

class User extends BaseModel
{
    use SoftDelete;

    /**
     * 获取用户拥有的角色ID列表
     *
     * @param int $uid
     * @return array
     */
    public function getRoleIdList(int $uid): array
    {
        $where = [
            ['id', '=', $uid]
        ];

        $info = $this->info('roles', $where);
        if (empty($info) || empty($info['roles'])) {
            return [];
        }

        return explode(',', $info['roles']);
    }
}
