<?php
/**
 * Created by PhpStorm.
 * User: jianfeichen
 * Date: 2020/5/9
 * Time: 14:32
 */

namespace msv\library;

use think\facade\Env;

/**
 * 画图
 * Class Drawing
 * @package app\common\library
 */
class Drawing
{
    protected $im;

    public function __construct($width, $height, $color)
    {
        $this->im = $this->canvas($width, $height, $color);
    }

    /**
     * 创建画布
     * @param $width
     * @param $height
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return resource
     */
    private function canvas($width, $height, $color)
    {
        list($red, $green, $blue) = $color;
        $im = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($im, $red, $green, $blue);
        imagefill($im, 0, 0, $color);
        return $im;
    }

    /**
     * 拷贝jpeg图片
     * @param $imgSrc - 要拷贝的图片连接
     * @param $dstPosition -目的坐标 x,y
     * @param $srcPosition - 源图目标x,y,width,height
     * @return $this
     */
    public function copyImage($imgSrc, $dstPosition, $srcPosition)
    {
        list($srcX, $srcY, $srcW, $srcH) = $srcPosition;
        list($dstX, $dstY, $dstW, $dstH) = $dstPosition;
        if (false === $srcW || false === $srcH) {
            $srcImageInfo = getimagesize($imgSrc);
            $width = Arr::get($srcImageInfo, 0);
            $height = Arr::get($srcImageInfo, 1);
            if ($width/$height>$dstW/$dstH) {//宽度大于高度
                $srcW = $height * $dstW / $dstH;
                $srcH = $height;
                $srcX = ($width - $srcW) / 2;
            } else {
                $srcW = $width;
                $srcH = $width * $dstH / $dstW;
                $srcY = ($height - $srcH) / 2;
            }
        }
        $string = file_get_contents($imgSrc);
        $srcImage = imagecreatefromstring($string);
        imagecopyresampled($this->im, $srcImage, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
        return $this;
    }

    /**
     * 写文字
     * @param $text
     * @param $color
     * @param int $size
     * @param int $float -旋转角度
     * @param int $x
     * @param int $y
     * @param string $fontFile
     * @return $this
     */
    public function write($text, $color, $size = 14, $lineHeight = 14, $float = 0, $position = [0, 0], $fontFile = '/static/myself/font/msyh.TTF')
    {
        list($x, $y) = $position;
        list($red, $green, $blue) = $color;
        $imColor = imagecolorallocate($this->im, $red, $green, $blue);
        $textArr = explode("\n", $text);
        foreach ($textArr as $item) {
            $y += $lineHeight;
            imagettftext($this->im, $size, $float, $x, $y, $imColor, $fontFile, $item);
        }
        return $this;
    }

    /**
     * 显示png图片
     * @param $im
     */
    public function showPng()
    {
        header("content-type:image/png");
        imagepng($this->im);
        imagedestroy($this->im);
        exit();
    }

    public function baseImage()
    {
        $filename = Env::get('root_path') . "public/upload/license." . time() . rand(10000, 99999) . ".jpg";
        imagepng($this->im, $filename);
        imagedestroy($this->im);
        $image_info = getimagesize($filename);
        $image_data = fread(fopen($filename, 'r'), filesize($filename));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        unlink($filename);
        return $base64_image;
    }


}