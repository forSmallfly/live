<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;
use think\model\concern\SoftDelete;

class Rule extends BaseModel
{
    use SoftDelete;

    /**
     * 根据URL获取权限ID
     *
     * @param string $url
     * @return int
     */
    public function getRuleIdFromUrl(string $url): int
    {
        $where = [
            ['url', '=', $url]
        ];

        return $this->fieldInfo('id', $where);
    }
}
