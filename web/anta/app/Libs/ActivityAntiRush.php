<?php
namespace App\Libs\activity;
/* Basic request URL */
define('URL', 'csec.api.qcloud.com/v2/index.php');

function sendRequest($url, $method = 'POST')
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
function makeURL($method, $action, $region, $secretId, $secretKey, $args)
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
            'sha1', $method . URL . '?' . makeQueryString($args, false),
            $secretKey, true
        )
    );

    /* Assemble final request URL */

    return 'https://' . URL . '?' . makeQueryString($args, true);
}

/* Construct query string from array */
function makeQueryString($args, $isURLEncoded)
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

/* TLV builder */
abstract class TLV
{
    private $type;
    private $value;

    /* Assemble a TLV item */
    public function assemble()
    {
        $len = strlen($this->value);
        return pack('NN', $this->type, $len) . $this->value;
    }

    /* Initialize TLV data */
    public function __construct($type, $value = '')
    {
        $this->type = $type;
        $this->value = $value;
    }
}

class Content    extends TLV { public function __construct($value) { parent::__construct(1, $value); } }
class ImageURL   extends TLV { public function __construct($value) { parent::__construct(2, $value); } }
class VideoURL   extends TLV { public function __construct($value) { parent::__construct(3, $value); } }
class AudioURL   extends TLV { public function __construct($value) { parent::__construct(4, $value); } }
class WebsiteURL extends TLV { public function __construct($value) { parent::__construct(5, $value); } }
class Emoticon   extends TLV { public function __construct($value) { parent::__construct(6, $value); } }
class Title      extends TLV { public function __construct($value) { parent::__construct(7, $value); } }
class Location   extends TLV { public function __construct()       { parent::__construct(8);         } }
class Custom     extends TLV { public function __construct()       { parent::__construct(9);         } }
class File       extends TLV { public function __construct()       { parent::__construct(10);        } }
class Other      extends TLV { public function __construct()       { parent::__construct(1000);      } }

/* Message-Struct constructor for API `UgcAntiSpam` */
function buildMessageStruct(/* varidic */)
{
    /* Assembled original binary result */
    $result = '';

    /* Assemble each Message-Struct item */
    foreach (func_get_args() as $tlv)
    {
        if (!($tlv instanceof TLV))
            throw new Exception('Item is not TLV');

        /* Append to result */
        $result .= $tlv->assemble();
    }

    /* Encode result with Base64 */
    return base64_encode($result);
}

/* Demo section */
/* 密钥,请进行替换,密钥申请地址 https://console.qcloud.com/capi  */
define('SECRET_ID', 'AKIDb6QdouwbBZlpjXnpGXhniVCi2CBH83e5');
define('SECRET_KEY', 'PdjiW8YmofJl274pOm02sX0HMFwSmyym');

function ActivityAntiRush($params, $region='gz')
{
    /*
    * 补充用户、行为信息数据,方便我们做更准确的数据模型
    * 协议参考 https://www.qcloud.com/doc/api/254/2910
    */
    $url = makeURL('GET', 'ActivityAntiRush', $region, SECRET_ID, SECRET_KEY, $params);
    $result = sendRequest($url);
    return $result;
}

function main() 
{
    $params = array(
        /* 账号信息 */
        'accountType'          => '5',
        'uid'                  => 'D692D87319F2098C3877C3904B304706',
        'associateAccount'     => '373909726',
        'nickName'             => 'helloword',
        'phoneNumber'          => '086+15166666666',
        'emailAddress'         => 'hellword@qq.com',
        'registerTime'         => '1440416972',
        'registerIp'           => '121.14.96.121',
        'passwordHash'         => 'f158abb2a762f7919846ee9bf8445c7f22a244c5',

        /* 行为信息 */
        'userIp'               => '14.17.22.32',
        'postTime'             => '1436664316',
        'loginSource'          => '4',
        'loginType'            => '3',
        'rootId'               => 'sdsds234sd',
        'referer'              => 'https://ui.ptlogin2.qq.com/cgi-bin/login',
        'jumpUrl'              => 'https://ui.ptlogin2.qq.com/cgi-bin/hello',
        'cookieHash'           => 'D692D87319F2098C3877C3904B304706',
        'userAgent'            => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.132 Safari/537.36',
        'xForwardedFor'        => '121.14.96.121',
        'mouseClickCount'      => '10',
        'keyboardClickCount'   => '34',

        /* 设备信息 */
        'macAddress'           => '00-05-9A-3C-7A-00',
        'vendorId'             => 'tencent.com',
        'imei'                 => '54654654646',
        'appVersion'           => '10.0.1',

        /* 其他信息 */
        'businessId'           => '1',
    );
    echo json_encode(ActivityAntiRush($params));
}

main();