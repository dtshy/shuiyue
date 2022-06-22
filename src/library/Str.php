<?php
/**
 * Created by PhpStorm.
 * User: jianfeichen
 * Date: 2019/7/25
 * Time: 15:24
 */

namespace msv\library;

/**
 * 字符串工具类
 * Class Str
 * @package app\common\library
 */
class Str
{

    /**
     * 截取字符串并自动补充省略号
     * @param $str
     * @param $start
     * @param $len
     * @param string $ellipsis
     * @return string
     */
    public static function subStr($str, $start, $len, $ellipsis = '…')
    {
        $sourceLen = mb_strlen($str);
        $str = mb_substr($str, $start, $len);
        if ($sourceLen > $len) {
            $str .= $ellipsis;
        }
        return $str;
    }

    /**
     * 根据身份号码返回出生年
     * @param $idCard
     * @return int
     */
    public static function getBirthYearByIdCard($idCard)
    {
        return self::subStr($idCard, 6, 4, '');
    }


    /**
     * 加密用户名称
     * @param $name
     * @return string
     */
    public static function secretName($name)
    {
        //1、获取名字长度
        $nameLen = mb_strlen($name);
        if ($nameLen <= 1) {
            return "···";
        }
        //2、获取姓氏
        $firstName = mb_substr($name, 0, 1);
        //3、隐藏后的名
        $lastName = str_repeat('·', $nameLen - 1);
        //4、完整名字
        $name = $firstName . $lastName;
        return $name;
    }

    /**
     * 加密用户手机号
     * @param $mobile
     * @return string
     */
    public static function secretMobile($mobile)
    {
        //1、手机号前三位
        $left = mb_substr($mobile, 0, 3);
        //2、手机号后四位
        $right = mb_substr($mobile, -4, 4);
        //3、加密后的完整手机号
        $mobile = $left . '****' . $right;
        return $mobile;
    }

    /**
     * 获取字符串中的数字
     * @param $str
     * @return mixed
     */
    public static function getNumFromStr($str)
    {
        $pattern = '/\d+/';
        preg_match_all($pattern, $str, $result);
        return intval(implode(Arr::get($result, 0)));
    }


    /**
     * 下划线转驼峰
     * 思路:
     * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     * @param $uncamelized_words
     * @param string $separator
     * @return string
     */
    public static function camelize($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }


    /**
     * 驼峰命名转下划线命名
     * 思路:
     * @param $camelCaps
     * @param string $separator
     * @return string
     */
    public static function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

}