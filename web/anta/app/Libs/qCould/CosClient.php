<?php
/**
 * 腾讯云对象存储COS接入类
 */
namespace App\Libs\qCould;

require_once('include.php');

use qcloudcos\Cosapi;

class CosClient
{
    private static $COS_BUCKET = 'bengong-1256861713';
    private static $COS_REGION = 'ap-beijing';             // 所在区域  华南: gz; 华中: sh;  华北: tj
    // private static $obj_cosapi = '';

    private static function init()
    {
        Cosapi::setRegion( self::$COS_REGION );
    }

    /**
     * 上传文件
     * @param string $src
     * @param string $dst
     * @return Mixed
     */
    public static function upload($src, $dst)
    {
        self::init();
        return Cosapi::upload(self::$COS_BUCKET, $src, $dst);
    }

    /**
     * 删除目录
     * @param $dirname
     * @return array|mixed
     */
    public static function deldir($dirname)
    {
        self::init();
        return Cosapi::delFolder(self::$COS_BUCKET, $dirname);
    }

    /**
     * 获取指定目录中的文件
     * @param $num
     * @return array|mixed
     */
    public static function getDirList($dirname, $num=20)
    {
        self::init();
        return Cosapi::listFolder(self::$COS_BUCKET, $dirname, $num);
    }

    /**
     * 删除指定目录的文件
     * @param $filename
     * @return array|mixed
     */
    public static function delFile($filename)
    {
        self::init();
        return Cosapi::delFile(self::$COS_BUCKET, $filename);
    }

}