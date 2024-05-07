<?php
/**
 * Created by PhpStorm.
 * User: jianfeichen
 * Date: 2019/8/29
 * Time: 19:20
 */

namespace msv\library;


class Time
{
    const YEAR = 31556926;
    const MONTH = 2629744;
    const WEEK = 604800;
    const DAY = 86400;
    const HOUR = 3600;
    const MINUTE = 60;

    const MONTH_ARR = [
        1 => '一月',
        2 => '二月',
        3 => '三月',
        4 => '四月',
        5 => '五月',
        6 => '六月',
        7 => '七月',
        8 => '八月',
        9 => '九月',
        10 => '十月',
        11 => '十一月',
        12 => '十二月'
    ];

    /**
     * 返回当天剩余时间戳
     * @return false|int
     */
    public static function todaySurplusTime()
    {
        $t = time();
        $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
        return $end - $t;
    }

    /**
     * 当天的开始和结束时间戳
     * @return array
     */
    public static function today()
    {
        $t = time();
        $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
        $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
        return [$start, $end];
    }

    /**
     * 昨天开始和结束时间戳
     * @return array
     */
    public static function yesterday()
    {
        $t = time();
        $start = mktime(0, 0, 0, date("m", $t), date("d", $t) - 1, date("Y", $t));
        $end = mktime(23, 59, 59, date("m", $t), date("d", $t) - 1, date("Y", $t));
        return [$start, $end];
    }

    /**
     * 最近七天的开始结束时间戳，结束到上一天24:00
     * @return array
     */
    public static function lastWeek()
    {
        $t = time();
        $start = mktime(0, 0, 0, date("m", $t), date("d", $t) - 7, date("Y", $t));
        $end = mktime(23, 59, 59, date("m", $t), date("d", $t) - 1, date("Y", $t));
        return [$start, $end];
    }

    /**
     * 获取本周开始和结束时间戳
     * @return array
     */
    public static function week()
    {
        // 设置时区为北京时间
        date_default_timezone_set('Asia/Shanghai');

        // 获取本周的开始时间戳
        $week_start = strtotime('monday this week');

        // 获取本周的结束时间戳
        $week_end = strtotime('sunday this week') + 86399; // 加上一天减去一秒
        return [$week_start, $week_end];
    }

    /**
     * 最近三十天开始结束时间戳，结束到上一天24:00
     * @return array
     */
    public static function lastMonth()
    {
        $t = time();
        $start = mktime(0, 0, 0, date("m", $t), date("d", $t) - 30, date("Y", $t));
        $end = mktime(23, 59, 59, date("m", $t), date("d", $t) - 1, date("Y", $t));
        return [$start, $end];
    }

    /**
     * 最近三十天开始结束时间戳，结束到上一天24:00
     * @return array
     */
    public static function preMonth()
    {
        $t = time();
        $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
        $end = mktime(23, 59, 59, date("m", $t), date("d", $t) + 30, date("Y", $t));
        return [$start, $end];
    }

    /**
     * 传入对应月份返回月份的开始和结束时间戳
     * @param $month 1/2/3/4/5/6/7/8/9/10/11/12
     * @return array
     */
    public static function monthTime($month, $year = null)
    {
        $start = mktime(0, 0, 0, $month, 1, $year ?: date("Y"));
        $end = mktime(0, 0, 0, $month + 1, 1, $year ?: date("Y")) - 1;
        return [$start, $end];
    }

    /**
     *传入对应季度获取开始和结束时间
     * @param $quarter 1/2/3/4
     * @param null $year
     * @return array
     */
    public static function quarter($quarter, $year = null)
    {
        $start = mktime(0, 0, 0, ($quarter - 1) * 3 + 1, 1, $year ?: date("Y"));
        $end = mktime(0, 0, 0, $quarter * 3 + 1, 1, $year ?: date("Y")) - 1;
        return [$start, $end];
    }

    /**
     * 传入对应年份返回年份的开始和结束时间戳
     * @param $year
     * @return array
     */
    public static function yearTime($year = null)
    {
        $start = mktime(0, 0, 0, 1, 1, $year ?: date("Y"));
        $end = mktime(0, 0, 0, 13, 1, $year ?: date("Y")) - 1;
        return [$start, $end];
    }

    /**
     * 格式化时间或日期
     * @param $format
     * @param $value
     * @return false|string
     */
    public static function format($format, $value)
    {
        return $value ? date($format, $value) : "";
    }


    /**
     * 将时间戳格式化成剩余小时
     * @param $timestamp
     * @return float
     */
    public static function formatTime($timestamp, $leavel = 'hour')
    {
        $unit = self::HOUR;
        switch ($leavel) {
            case 'hour':
                $unit = self::HOUR;
                break;
            case 'day':
                $unit = self::DAY;
                break;
        }
        return round($timestamp / $unit, 1);
    }

    /**
     * 获取星期几
     * @param $data
     * @return string
     */
    public static function getWeek($data)
    {
        $number = date("N", strtotime($data));
        $week = '';
        switch ($number) {
            case 1:
                $week = '一';
                break;
            case 2:
                $week = '二';
                break;
            case 3:
                $week = '三';
                break;
            case 4:
                $week = '四';
                break;
            case 5:
                $week = '五';
                break;
            case 6:
                $week = '六';
                break;
            case 7:
                $week = '日';
                break;
            default:
                ;
        }
        return $week;
    }

    /**
     * 获取当前几号
     * @param string $date
     * @return false|string
     */
    public static function getDayNum($date = '')
    {
        $time = $date ? strtotime($date) : time();
        $dayNum = date("d", $time);
        return $dayNum;
    }

    /**
     * 根据时间差格式化时间戳,不足一小时，显示多少分钟前，不足一天显示多少小时前
     * @param $value
     * @return false|string
     */
    public static function getDiffFormatTime($value)
    {
        $now = time();
        $diffTime = $now - $value;
        if ($diffTime == 0) {
            $result = '1秒钟前';
        } else if ($diffTime < Time::MINUTE) {
            $result = $diffTime . '秒钟前';
        } else if ($diffTime < Time::HOUR) {
            $result = ceil($diffTime / Time::MINUTE) . "分钟前";
        } else if ($diffTime < Time::DAY) {
            $result = round($diffTime / Time::HOUR) . "小时前";
        } else {
            $result = date('Y-m-d', $value);
        }
        return $result;
    }


    /**
     * 根据时间差格式化时间戳,不足十分钟显示刚刚，不足一小时，显示多少分钟前，不足一天显示多少小时前,不足一个月显示多少天前，不足一年显示多少月前，大于一年显示多少年前
     * @param $value
     * @return false|string
     */
    public static function getDiffFormatTimeAll($value)
    {
        $now = time();
        $diffTime = $now - $value;
        if ($diffTime == 0) {
            $result = '刚刚';
        } else if ($diffTime < 10 * Time::MINUTE) {
            $result = '刚刚';
        } else if ($diffTime < Time::HOUR) {
            $result = ceil($diffTime / Time::MINUTE) . "分钟前";
        } else if ($diffTime < Time::DAY) {
            $result = round($diffTime / Time::HOUR) . "小时前";
        } else if ($diffTime < Time::MONTH) {
            $result = round($diffTime / Time::DAY) . "天前";
        } else if ($diffTime < Time::YEAR) {
            $result = round($diffTime / Time::MONTH) . "月前";
        } else {
            $result = round($diffTime / Time::YEAR) . "年前";
        }
        return $result;
    }


}