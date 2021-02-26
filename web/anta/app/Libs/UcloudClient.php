<?php

/**
 * ucloud对象存储接入类
 */
namespace App\Libs;

require_once(dirname(__FILE__).'/ucloud/proxy.php');


class UcloudClient
{
    #private static $bucket = 'osv'; // 存储空间名
    private static $bucket = 'anta'; // 存储空间名

    /**
     * @param $bucket 存储空间名
     * @param $filename 上传之后文件的名称  例如：test.png
     * @param $filepath 上传文件的地址      例如：storage_path('app/public/').'test.png'
     */
    public static function upload($filename, $filepath)
    {
        //初始化分片上传,获取本地上传的uploadId和分片大小
        list($data, $err) = UCloud_MInit(self::$bucket, $filename);
        if ($err)
        {
            return [ 'code'=>$err->Code, 'msg'=>$err->ErrMsg ];
        }
        $uploadId = $data['UploadId'];
        $blkSize  = $data['BlkSize'];

        //数据上传
        list($etagList, $err) = UCloud_MUpload(self::$bucket, $filename, $filepath, $uploadId, $blkSize);
        if ($err) {
            return [ 'code'=>$err->Code, 'msg'=>$err->ErrMsg ];
        }
        //完成上传
        list($data, $err) = UCloud_MFinish(self::$bucket, $filename, $uploadId, $etagList);
        if ($err) {
            return [ 'code'=>$err->Code, 'msg'=>$err->ErrMsg ];
        }
        return ['code'=>0, 'msg'=>'上传成功'];
    }

    /**
     * 小文件上传  小于10M
     * @param $filename 上传之后文件的名称
     * @param $filepath 上传的文件路径
     */
    public static function minfileup($filename, $filepath)
    {
        list($data, $err) = UCloud_PutFile(self::$bucket, $filename, $filepath);
        if ($err) {
            return [ 'code'=>$err->Code, 'msg'=>$err->ErrMsg ];
        }
        return ['code'=>0, 'msg'=>'上传成功'];
    }

    public static function makepriurl($filename)
    {
        $curtime = time();
        $curtime += 60; // 有效期60秒
        $url = UCloud_MakePrivateUrl(self::$bucket, $filename, $curtime);
        $content = self::curl_file_get_contents($url);
        return $url;
    }

    public static function curl_file_get_contents($durl){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $durl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
}