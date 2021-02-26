<?php

namespace App\Libs;

use Qcloud;

class Cos
{
    const APP_ID     = '1256861713';
    const SECRET_ID  = 'AKIDkQuDPQLGMd8ey8kAzhEcotoXJyQjuvui';
    const SECRET_KEY = 'zo0LYaTBnEUluAHC1eImuvnTw32whIhw';
    const COS_REGION = 'ap-beijing';
    const BUCKED = 'bengong-1256861713';

    /**
     * @param $file 文件流
     * @param $filename
     * @return mixed
     */
    public static function upload($file, $filename)
    {
        $cosClient = new Qcloud\Cos\Client(array('region' => self::COS_REGION,
            'credentials'=> array(
                'secretId'    => self::SECRET_ID,
                'secretKey' => self::SECRET_KEY)));

        $result = $cosClient->putObject(array(
            'Bucket' => self::BUCKED,
            'Key' => $filename,
            'Body' => $file));
        $url = false;
        foreach ($result as $item) {
            if (substr((string)$item, 0, 7) == 'http://') {
                $url = urldecode($item);
            }
        }
        $url = str_replace("http://","https://",$url);
        return $url;
    }
}