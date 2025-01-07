<?php
// 应用公共文件
use app\Request;

if (!function_exists('getRequest')) {
    /**
     * 返回请求对象
     *
     * @return Request
     */
    function getRequest(): Request
    {
        return app()->make('request');
    }
}

if (!function_exists('now')) {
    /**
     * 获取当前时间
     *
     * @return string
     */
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}