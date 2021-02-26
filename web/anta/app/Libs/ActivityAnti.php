<?php
namespace App\Libs;


/* Basic request URL */
define('URL', 'csec.api.qcloud.com/v2/index.php');

/* Demo section */
/* 密钥,请进行替换,密钥申请地址 https://console.qcloud.com/capi  */
define('SECRET_ID', 'AKIDb6QdouwbBZlpjXnpGXhniVCi2CBH83e5');
define('SECRET_KEY', 'PdjiW8YmofJl274pOm02sX0HMFwSmyym');

class ActivityAnti {

    public static function sendRequest($url, $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (false !== strpos($url, "https")) {
            // 证书
            // curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $resultStr = curl_exec($ch);
        $result = json_decode($resultStr, true);

        return $result;
    }

    /* Generates an available URL */
    public static function makeURL($method, $action, $region, $secretId, $secretKey, $args)
    {
        /* Add common parameters */
        $args['Nonce'] = (string)rand(0, 0x7fffffff);
        $args['Action'] = $action;
        $args['Region'] = $region;
        $args['SecretId'] = $secretId;
        $args['Timestamp'] = (string)time();

        /* Sort by key (ASCII order, ascending), then calculate signature using HMAC-SHA1 algorithm */
        ksort($args);
        $args['Signature'] = base64_encode(
            hash_hmac(
                'sha1', $method . URL . '?' . self::makeQueryString($args, false),
                $secretKey, true
            )
        );

        /* Assemble final request URL */

        return 'https://' . URL . '?' . self::makeQueryString($args, true);
    }

    /* Construct query string from array */
    public static function makeQueryString($args, $isURLEncoded)
    {
        $arr = array();
        foreach ($args as $key => $value) {
            if (!$isURLEncoded) {
                $arr[] = "$key=$value";
            } else {
                $arr[] = $key . '=' . urlencode($value);
            }
        }
        return implode('&', $arr);
    }

    public static function ActivityAntiRush($params, $region='gz')
    {
        /*
        * 补充用户、行为信息数据,方便我们做更准确的数据模型
        * 协议参考 https://www.qcloud.com/doc/api/254/2910
        */
        $url = self::makeURL('GET', 'ActivityAntiRush', $region, SECRET_ID, SECRET_KEY, $params);
        $result = self::sendRequest($url);
        return $result;
    }

    public static function main($data)
    {
        $params = array(
            /* 账号信息 */
            'accountType'          => '10004',
            'uid'                  => md5($data['phone']),

            /* 行为信息 */
            'userIp'               => $data['ip'],
            'postTime'             => $data['time'],

        );
        return self::ActivityAntiRush($params);
    }
}