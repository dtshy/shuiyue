<?php
/**
 * Created by PhpStorm.
 * User: jianfeichen
 * Date: 2020/4/3
 * Time: 11:03
 */

namespace app\common\library;


class Process
{
    private $processNum = 2;
    private $timeout = 3000;//程序预计执行时间，超过此设置时间没有执行完，将不再等待进程回收
    private $execArr = array();

    public function execute()
    {
        //$log = Log::instance();
        //判断pcntl扩展是否开启
        if ( ! function_exists('pcntl_fork'))
        {
            //$log->add('ERR', 'pcntl扩展未开启！');
            die('pcntl扩展未开启！');
        }

        //创建管道
        $sPipePath = 'my_pipe'.posix_getpid();
        if ( ! posix_mkfifo($sPipePath, 0666))
        {
            die('创建管道'.$sPipePath.'错误');
        }
        //Core_Worm::fileLog('pcntl start!', 'w+');
        for ($i = 0; $i < $this->getpNum(); $i ++)
        {
            $nPID = pcntl_fork();//创建一个子进程
            if ($nPID == 0)
            {
                echo $i."\n";
                //子进程过程
                if (empty($this->execArr))
                {
                    die('进程执行数组为空，');
                }
                else
                {
                    call_user_func($this->execArr[$i]);
                }

                $oW = fopen($sPipePath, 'w');
                fwrite($oW, $i."\n"); // 当前任务处理完比，在管道中写入数据
                fclose($oW);
                exit(0);
            }
            sleep(3);//每启动一个进程休眠3秒，避免一次进程启动太多造成redis或者mysql读写出错
        }
        //父进程
        $fpid = fopen($sPipePath, 'r');
        stream_set_blocking($fpid, FALSE);//将管道设置为非堵塞，用于适应超时机制
        $sData  = ''; //存放管道中的数据
        $nLine  = 0;
        $nStart = time();
        while ($nLine < $this->getpNum() && (time() - $nStart) < $this->getTimeout())
        {
            $sLine = fread($fpid, 1024);
            if (empty($sLine))
            {
                continue;
            }
            echo 'current line:'.$sLine;
            //用于分析多少任务处理完毕，通过'\n'标识
            foreach ($arr = str_split($sLine) as $c)
            {
                //echo Debug::vars($arr);
                if ("\n" == $c)
                {
                    ++ $nLine;
                }
            }
            $sData .= $sLine;
        }

        echo "Final line count:$nLine\n";
        fclose($fpid);
        unlink($sPipePath);

        //等待子进程执行完毕，避免僵尸进程
        $n = 0;
        while ($n < $this->getpNum())
        {
            $nStatus = - 1;
            $nPID    = pcntl_wait($nStatus, WNOHANG);
            if ($nPID > 0)
            {
                echo "{$nPID} exit \n";
                ++ $n;
            }
        }

        //验证结果，主要看结果中是否每个任务都完成了
        $arr2 = array();
        foreach (explode("\n", $sData) as $i)
        {
            if (is_numeric(trim($i)))
            {
                array_push($arr2, $i);
            }
        }
        $arr2 = array_unique($arr2);
        if (count($arr2) == $this->getpNum())
        {
            echo "ok\n";
        }
        else
        {
            echo "error count ".count($arr2)."\n";
            var_dump($arr2);
        }
    }

    public function setExecArr($array)
    {
        $this->execArr = $array;
    }

    public function setTimeout($secends)
    {
        $this->timeout = $secends;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setpNum($num)
    {
        $this->processNum = $num;
    }

    public function getpNum()
    {
        return $this->processNum;
    }
}