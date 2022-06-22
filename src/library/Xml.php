<?php
/**
 * Created by PhpStorm.
 * User: jianfeichen
 * Date: 2019/7/31
 * Time: 9:38
 */

namespace app\common\library;


class Xml
{
    /**
     * 将xml字符串转换成对象
     * @param $xml
     * @return \SimpleXMLElement
     */
    public static function toObject($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        return $xml;
    }

    /**
     * 将xml字符串转换成数组
     * @param $xml
     * @return mixed
     */
    public static function toArray($xml)
    {
        $xml = self::toObject($xml);
        return json_decode(json_encode($xml), true);
    }

}