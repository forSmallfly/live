<?php

namespace app\utils;

use app\BaseValidate;
use app\Request;

/**
 * 请求工具库
 */
trait RequestTool
{
    /**
     * 判断是否在免登录名单中
     *
     * @return bool
     */
    private function isInExemptLoginList(): bool
    {
        $rule = $this->getCurrentUrl();

        return in_array($rule, config('api.exempt_login'));
    }

    /**
     * 判断是否在接口白名单中
     *
     * @return bool
     */
    private function isInApiWhiteList(): bool
    {
        $rule = $this->getCurrentUrl();

        return in_array($rule, config('api.white_list'));
    }

    /**
     * 获取当前请求URL
     *
     * @return string
     */
    private function getCurrentUrl(): string
    {
        return getRequest()->controller() . '/' . getRequest()->action();
    }

    /**
     * 获取请求参数
     *
     * @param Request $request
     * @return array
     */
    private function getParams(Request $request): array
    {
        $method = strtolower($request->method());
        return match ($method) {
            'get'    => $request->get(),
            'post'   => $request->post(),
            'put'    => $request->put(),
            'delete' => $request->delete(),
            default  => [],
        };
    }

    /**
     * 过滤请求参数
     *
     * @param string $scene
     * @param BaseValidate $validate
     * @param array $params
     * @return array
     */
    private function filterParams(string $scene, BaseValidate $validate, array $params): array
    {
        // 获取定义的验证规则
        $ruleList = $validate->getRuleList();

        // 获取定义的验证场景列表
        $sceneList = $validate->getSceneList();

        // 验证场景列表存在当前场景时，直接使用验证场景中的字段信息
        if (isset($sceneList[$scene])) {
            $fieldList = $sceneList[$scene];
        } else {
            // 获取场景需要验证的规则
            $onlyField = $validate->getOnly();
            // 有自定义验证场景时，有指定场景需要验证的字段，直接使用
            if (!empty($onlyField)) {
                $fieldList = $onlyField;
            } else {
                // 没有指定场景需要验证的字段
                $ruleKeyList = array_keys($ruleList);

                // 剔除移除的字段
                $removeList = $validate->scene($scene)->getRemove();
                if (!empty($removeList)) {
                    foreach ($ruleKeyList as $key => $fieldName) {
                        if (isset($removeList[$fieldName]) && $removeList[$fieldName] === true) {
                            unset($ruleKeyList[$key]);
                        }
                    }
                }

                // 添加新增的字段
                $appendList = $validate->scene($scene)->getAppend();
                if (!empty($appendList)) {
                    $appendKeyList = array_keys($appendList);
                    $ruleKeyList   = array_merge($ruleKeyList, $appendKeyList);
                }

                $fieldList = array_values($ruleKeyList);
            }
        }

        // 请求参数数据类型转换
        foreach ($params as $filedName => $value) {
            // 字段有规定数字类型进行数据类型转换
            if (!empty($ruleList[$filedName]) && in_array('integer', $ruleList[$filedName])) {
                $params[$filedName] = (int)$value;
            }
        }

        // 过滤请求参数字段
        return array_filter($params, function ($filedName) use ($fieldList) {
            return in_array($filedName, $fieldList);
        }, ARRAY_FILTER_USE_KEY);
    }
}