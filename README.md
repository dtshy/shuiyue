# shuiyue 洞天水月

#### 介绍
水月的工具仓库，thinkphp6.0下基础Model、Service类、Validate验证类的生成。包括一些基础的数组、字符串、日期等的处理函数。

#### 软件架构
src/library 基础公共函数
src/service 生成MSV的核心类文件
src/tpl 生成MSV的模板文件


#### 安装教程

1.  安装扩展包 composer require shuiyue/msv
2.  自定义控制器，创建app\common\command\MSV.php文件

```$xslt
<?php
declare (strict_types=1);

namespace app\command;

use msv\service\MSVService;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class MSV extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('create')
            ->addOption('table', 't', Option::VALUE_REQUIRED, 'table name')
            ->setDescription('创建MSV(Model Service Validate)指令');
    }

    protected function execute(Input $input, Output $output)
    {
        $result = "";
        $table = trim($input->getOption('table'));
        $baseModel = MSVService::instance()->create_base_model();
        $model = MSVService::instance()->create_model($table);
        $baseService = MSVService::instance()->create_base_service();
        $service = MSVService::instance()->create_service($table);
        $validate = MSVService::instance()->create_validate($table);

        $result .= $baseModel . "\n";
        $result .= $model . "\n";
        $result .= $baseService . "\n";
        $result .= $service . "\n";
        $result .= $validate . "\n";
        // 指令输出
        $output->writeln($result);
    }
}

```
3  修改config/console.php 增加
```$xslt
 'msv'=>\app\command\MSV::class
```

#### 使用说明

1.  配置env文件的数据库连接
2.  应用根目录执行php think msv -t tablename
3.  查看创建的文件ls app/common

#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request



官网地址：https://www.55blog.cn
你可以通过官网联系到作者。
