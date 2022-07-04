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


    /**
    * 获取列表
    * @return \think\response\Json
    */
    public function getList($limit, $where = [], $order = [], $with = [], $withCount = [], $page = null)
        {
            if (empty($this->model)) {
            return error('Service model属性不存在！');
        }
        if (empty($order)) {
            $order['create_time'] = 'desc';
        }
        $model = new $this->model;
        !empty($with) && $model = $model->with($with);
        !empty($withCount) && $model = $model->withCount($withCount);
        !empty($where) && $model = $model->where($where);

        $result = $model->order($order)->paginate($limit, false, ['page' => $page]);
        return $result;
    }

    /**
    * 获取单个详情
    * @param $id
    * @param array $with
    * @return bool|\think\response\Json
    */
    public function getOne($id, $with = [])
    {
        if (empty($this->model)) {
            return false;
        }
        if (empty($id)) {
            return false;
        }
        $model = new $this->model;
        $data = $model->with($with)->find($id);
        return $data;
    }
}