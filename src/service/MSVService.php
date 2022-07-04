<?php
/**
 * Created by PhpStorm.
 * User: jianfeichen
 * Date: 2022/6/10
 * Time: 17:17
 */

namespace msv\service;

use msv\library\Arr;
use msv\library\Str;
use think\facade\Db;

class MSVService extends Service
{
    protected $tplPath = "/vendor/shuiyue/msv/src/tpl";

    //创建模型
    public function create_model($table)
    {
        //模型文件名
        $modelName = Str::camelize($table) . "Model.php";
        $modelPath = app_path() . '/common/model/';

        $modelTpl = root_path() . $this->tplPath . DIRECTORY_SEPARATOR . 'modelTpl.tpl';
        $fp = fopen($modelTpl, "r");
        $modelStr = fread($fp, filesize($modelTpl));//指定读取大小，这里把整个文件内容读取出来
        $modelStr = str_replace('{#modelName}', Str::camelize($table) . 'Model', $modelStr);
        $modelStr = str_replace('{#tableName}', "'" . $table . "'", $modelStr);

        $isCreate = $this->create_file($modelName, $modelPath, $modelStr);
        if ($isCreate) {
            $createResult = 'create file ' . $modelName . ' success!';
        } else {
            $createResult = 'create file ' . $modelName . ' fail!';
        }
        return $createResult;
    }

    /**
     * 创建基础模型
     * @return string
     */
    public function create_base_model()
    {
        $baseModelTpl = root_path() . $this->tplPath . DIRECTORY_SEPARATOR . 'baseModelTpl.tpl';
        $fp = fopen($baseModelTpl, "r");
        $modelStr = fread($fp, filesize($baseModelTpl));//指定读取大小，这里把整个文件内容读取出来

        $modelName = "BaseModel.php";
        $modelPath = app_path() . '/common/model/';

        $isCreate = $this->create_file($modelName, $modelPath, $modelStr);
        if ($isCreate) {
            $createResult = 'create file ' . $modelName . ' success!';
        } else {
            $createResult = 'create file ' . $modelName . ' fail!';
        }

        return $createResult;
    }

    //创建Service
    public function create_service($table)
    {
        //模型文件名
        $serviceName = Str::camelize($table) . "Service.php";
        $servicePath = app_path() . '/common/service/';

        $serviceTpl = root_path() . $this->tplPath . DIRECTORY_SEPARATOR . 'serviceTpl.tpl';
        $fp = fopen($serviceTpl, "r");
        $serviceStr = fread($fp, filesize($serviceTpl));//指定读取大小，这里把整个文件内容读取出来
        $serviceStr = str_replace('{#modelName}', Str::camelize($table) . 'Model', $serviceStr);
        $serviceStr = str_replace('{#serviceName}', Str::camelize($table) . 'Service', $serviceStr);

        $isCreate = $this->create_file($serviceName, $servicePath, $serviceStr);
        if ($isCreate) {
            $createResult = 'create file ' . $serviceName . ' success!';
        } else {
            $createResult = 'create file ' . $serviceName . ' fail!';
        }
        return $createResult;
    }

    public function create_base_service()
    {
        $baseServiceTpl = root_path() . $this->tplPath . DIRECTORY_SEPARATOR . 'baseServiceTpl.tpl';
        $fp = fopen($baseServiceTpl, "r");
        $serviceStr = fread($fp, filesize($baseServiceTpl));//指定读取大小，这里把整个文件内容读取出来

        $serviceName = "Service.php";
        $servicePath = app_path() . '/common/service/';

        $isCreate = $this->create_file($serviceName, $servicePath, $serviceStr);
        if ($isCreate) {
            $createResult = 'create file ' . $serviceName . ' success!';
        } else {
            $createResult = 'create file ' . $serviceName . ' fail!';
        }

        return $createResult;
    }

    //创建Validate
    public function create_validate($table)
    {
        //模型文件名
        $validateName = Str::camelize($table) . "Validate.php";
        $validatePath = app_path() . 'common/validate/';

        $validateTpl = root_path() . $this->tplPath . DIRECTORY_SEPARATOR . 'validateTpl.tpl';
        $fp = fopen($validateTpl, "r");
        $validateStr = fread($fp, filesize($validateTpl));//指定读取大小，这里把整个文件内容读取出来
        $validateStr = str_replace('{#validateName}', Str::camelize($table) . 'Validate', $validateStr);

        //获取数据字段
        $tableColumns = Db::query("SHOW FULL COLUMNS FROM " . $table);
        $rule = [];
        $scene = [
            'create' => [],
            'edit' => [],
            'id' => ['id']
        ];


        foreach ($tableColumns as $column) {
            $value = [];
            $key = Arr::get($column, 'Field') . '|' . Arr::get($column, 'Comment');
            if (Arr::get($column, 'Null') === 'NO') {
                array_push($value, 'require');
            }
            if (in_array(Arr::get($column, 'Field'), ['create_time', 'update_time', 'delete_time'])) {
                continue;
            }
            //获取字段类型
            $oldType = Arr::get($column, 'Type');
            list($type, $length) = $this->analysisMysqlType($oldType);
            if (in_array($type, ['int', 'tinyint'])) {
                array_push($value, 'number');
            }
            if (in_array($type, ['varchar', 'char'])) {
                array_push($value, 'max##' . $length);
            }
            $value = implode('|', $value);
            $rule[$key] = $value;
            if (Arr::get($column, 'Field') !== "id") {
                array_push($scene['create'], Arr::get($column, 'Field'));
            }
            array_push($scene['edit'], Arr::get($column, 'Field'));
        }
        $ruleStr = json_encode($rule, JSON_UNESCAPED_UNICODE);
        $ruleStr = str_replace('{', '[', $ruleStr);
        $ruleStr = str_replace('}', ']', $ruleStr);
        $ruleStr = str_replace(':', '=>', $ruleStr);
        $ruleStr = str_replace('##', ':', $ruleStr);
        $validateStr = str_replace('{#ruleArr}', $ruleStr, $validateStr);

        $sceneStr = json_encode($scene, JSON_UNESCAPED_UNICODE);
        $sceneStr = str_replace('{', '[', $sceneStr);
        $sceneStr = str_replace('}', ']', $sceneStr);
        $sceneStr = str_replace(':', '=>', $sceneStr);

        $validateStr = str_replace('{#sceneArr}', $sceneStr, $validateStr);
        $isCreate = $this->create_file($validateName, $validatePath, $validateStr);
        if ($isCreate) {
            $createResult = 'create file ' . $validateName . ' success!';
        } else {
            $createResult = 'create file ' . $validateName . ' fail!';
        }
        return $createResult;
    }


    /**
     * 创建文件
     * @param $fileName
     * @param $filePath
     * @param $content
     * @return bool
     */
    protected function create_file($fileName, $filePath, $content)
    {
        //文件夹是否存在
        $pathExist = is_dir($filePath);
        if (!$pathExist) {
            mkdir($filePath, '0755', true);
        }

        $modelExists = file_exists($filePath . $fileName);
        $createResult = false;
        if (!$modelExists) {
            //文件是否存在
            $file = fopen($filePath . $fileName, 'w');
            fwrite($file, $content);
            fclose($file);
            $createResult = true;
            if (!$file) {
                $createResult = false;
            }
        }

        return $createResult;
    }

    /**
     * 解析Mysql数据库类型
     * @param $type
     * @return array
     */
    public function analysisMysqlType($type)
    {
        if (in_array($type, ['text', 'tinytext'])) {
            return [$type, 0];
        }

        $typeArr = explode('(', $type);
        if (count($typeArr) === 2) {
            list($type, $length) = $typeArr;
            $length = str_replace(')', '', $length);
            return [$type, $length];
        }
        return ['未知类型', 0];
    }
}