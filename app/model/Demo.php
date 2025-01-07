<?php
declare (strict_types = 1);

namespace app\model;

use app\BaseModel;
use think\model\concern\SoftDelete;

class Demo extends BaseModel
{
    use SoftDelete;
}
