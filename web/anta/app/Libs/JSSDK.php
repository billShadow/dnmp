<?php
namespace App\Libs;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Redis;
class JSSDK {
    // 大亮的
    private $appid = 'wxbff64a0e0b4b624e';
    private $secrect = 'a368eab8ab96f56d2d13ba5d775d83d5';
    private $accessToken;



  public function __construct() {
      $this->accessToken = $this->getToken();
  }

    /**
     * @param $appid
     * @param $appsecret
     * @return mixed
     * 获取token
     */
    public function getToken() {
        $access_token = Redis::get('xcx_access_token');
        if (!$access_token) {
            // 如果是企业号用以下URL获取access_token
            //$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secrect";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                Redis::setex('xcx_access_token', 7000, $access_token);
            }
        } else {
            return $access_token;
        }
        return $access_token;
    }

    /**
     * 推送消息
     * @param $touser  openid
     * @param $template_id  模板id
     * @param string $page  点击模板跳转地址
     * @param $data         模板数据
     * @param $formId
     * @param string $color 字体颜色
     * @return mixed
     */
    public function sendTemplate($touser,$template_id,$page = 'pages/integrals/integrals',$data,$formId,$color = '#173177')
    {
        $template = array(
            'touser' => $touser,
            'template_id' => $template_id,
            'page' => $page,
            'form_id' => $formId,
            'data' => $data,
        );
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $this->accessToken;
        $dataRes = Curl::to($url)
            ->withData(urldecode($json_template))
            ->post();
        $dataRes = json_decode($dataRes,true);
        return $dataRes;
    }


    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appid,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }


    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->getToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }

    /**
     * 发送get请求
     * @param string $url
     * @return bool|mixed
     */
    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
    
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
    /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    public function httpRequest($url, $post, $header = array(), $connectTimeout = 15, $readTimeout = 300)
    {
        if (function_exists('curl_init')) {
            $timeout = $connectTimeout + $readTimeout;
            $ch = curl_init();
            if (strpos($url, 'https://') !== false) {	// HTTPS
                //curl_setopt($ch, CURLOPT_SSLVERSION, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if ($post == 'get') {
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
    }
}

