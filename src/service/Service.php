<?php
/**
 * Created by PhpStorm.
 * User: jianfeichen
 * Date: 2019/7/22
 * Time: 17:58
 */

namespace msv\service;

use think\Container;

class Service
{
    protected $model = '';

    /**
     * 静态实例对象
     * @param array $args
     * @return static
     */
    public static function instance(...$args)
    {
        return Container::getInstance()->make(static::class, $args);
    }
}