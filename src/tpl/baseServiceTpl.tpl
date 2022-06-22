<?php

namespace app\common\service;

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