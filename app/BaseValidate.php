<?php

namespace app;

use think\Validate;

/**
 * 验证基础类
 */
class BaseValidate extends Validate
{
    /**
     * 获取定义的验证规则
     *
     * @return array
     */
    public function getRuleList(): array
    {
        return $this->rule;
    }

    /**
     * 获取定义的验证场景列表
     *
     * @return array
     */
    public function getSceneList(): array
    {
        return $this->scene;
    }

    /**
     * 获取场景需要验证的规则
     *
     * @return array
     */
    public function getOnly(): array
    {
        return $this->only;
    }

    /**
     * 获取场景需要移除的验证规则
     *
     * @return array
     */
    public function getRemove(): array
    {
        return $this->remove;
    }

    /**
     * 获取场景需要追加的验证规则
     *
     * @return array
     */
    public function getAppend(): array
    {
        return $this->append;
    }
}