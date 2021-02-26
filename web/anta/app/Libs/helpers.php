<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
use App\Models\blessing_record;
use App\Models\user_formid;
use Ixudra\Curl\Facades\Curl;
use App\Libs\WeChat\WxSmallClient;
/**
 * 全局自定义函数
 */
define('TOKEN_KEY', 'experience_token_');

const KT_LOGINS = 'kt_logins';

const ERR_NOT_PARAM = '缺少参数';
const ERR_PARAM = '参数异常！';
const ERR_SERVICE = '服务器繁忙，请稍后在试！';

const RECORD_ENERGY = 'energy';
const RECORD_CARD = 'card';
const RECORD_ORDINARY = 'ordinary';

const RUN_HASH = "run_hash";
const CARD_QUEUE = 'card_queue';
const QUEUE_UNIS = 'queue_unis'; //通过unionid的接口发券。无法直接发券成功的放入此队列后台进程发券
const ANTA_AUTH_PATH = 'pages/market/voucher/voucher?type=coupon&userinfo=1';


 if (! function_exists('get_time')) {
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     */
    function get_time()
    {
        echo time();
    }
}

if (! function_exists('get_freight')) {
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     */
    function get_freight()
    {
        return 7;
    }
}



if (! function_exists('judge_price')) {
    /**
     * 递归创建目录
     */
    function judge_price($price)
    {
        //判断价格
        $rule  = "/^[1-9]\\d*$/";
        $result = preg_match($rule,$price);

        $rule  = "/^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*$/";
        $results = preg_match($rule,$price);
        if(!$result && !$results){
            return false;
        }
    }
}

if (! function_exists('mkDirs')) {
    /**
     * 递归创建目录
     */
    function mkDirs($dir)
    {
        if ( !is_dir($dir) ) {
            if ( !mkDirs(dirname($dir)) ) {
                return false;
            }
            if ( !mkdir($dir,0777) ) {
                return false;
            }
        }
        return true;
    }
}

if (! function_exists('fun_respon')) {
    /**
     *  return json maxed
     */
    function fun_respon($success, $res = [], $code = 200)
    {
        $result['result'] = $success;

        if (in_array($success, [400, 404])) {
            $result['result'] = $success;
            $result['msg'] = $res;
            $result['code'] = $code;
        } elseif ($success == 1) {
            $result['result'] = 1;
            $result['msg'] = '操作成功';
            $result['data'] = $res;
            $result['code'] = 200;
        } else {
            $result['result'] = 0;
            $result['msg'] = $res;
            $result['code'] = $code;
        }
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
        }
        exit(json_encode($result));
    }
}

if (! function_exists('anta_respon')) {
    /**
     *  return json maxed
     */
    function anta_respon($success, $res='操作成功')
    {
        $result['result'] = $success;

        if ($success == 1) {
            $result['result'] = 1;
            $result['data'] = $res;
        }else {
            $result['result'] = 0;
            $result['msg'] = $res;
        }
        exit(json_encode($result));
    }
}

if (! function_exists('ajax_respon')) {
    /**
     *  return json maxed
     */
    function ajax_respon($success, $res = [], $code = 200)
    {
        $result['result'] = $success;

        if ($success == 400) {
            $result['result'] = 400;
            $result['msg'] = $res;
            $result['code'] = $code;
        } elseif ($success == 1) {
            $result['result'] = 1;
            $result['msg'] = '操作成功';
            $result['data'] = $res;
            $result['code'] = $code;
        } else {
            $result['result'] = 0;
            $result['msg'] = $res;
            $result['code'] = $code;
        }
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($result));
    }
}

if (!function_exists('fun_img')) {
    /**
     * 拼接系统内图片url
     * @param $img
     * @return string
     */
    function fun_img($img){
        if (empty($img)) {
            return '';
        } elseif(strpos(strtolower($img), 'http://') === 0 || strpos(strtolower($img), 'https://') === 0 ) {
            return $img;
        } else {
            //return env('CDN_URL').$img;
            return 'https://minappcdn.mcdonalds.com.cn'.$img;
        }
    }
}

if (! function_exists('fun_respon_head')) {
    /**
     *  return json maxed
     */
    function fun_respon_head($success, $res = [], $code = 200)
    {
        $result['result'] = $success;

        if ($success) {
            $result['result'] = 1;
            $result['msg'] = '操作成功';
            $result['data'] = $res;
            $result['code'] = 200;
        } else {
            $result['result'] = 0;
            $result['msg'] = $res;
            $result['code'] = $code;
        }
        header("Content-Type: application/json; charset=UTF-8");
        exit(json_encode($result));
    }
}

if (!function_exists('fun_curl')) {
    /**
     * json curl request
     * @param $url
     * @param $data
     * @param $token
     * @return mixed
     */
    function fun_curl($url,$data){
        $ch = curl_init();
        //print_r($ch);
        curl_setopt( $ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时

        if(0 === strpos(strtolower($url), 'https'))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
        }
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type:text/xml'] );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }

}

if (!function_exists('fun_curl_header')) {
    /**
     * json curl request
     * @param $url
     * @param $data
     * @param $token
     * @return mixed
     */
    function fun_curl_header($url,$data, $header=['Content-Type:application/json']){
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时

        if(0 === strpos(strtolower($url), 'https'))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
        }
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }

}

if (!function_exists('arrayToXml')) {
    /**
     *  作用：array转xml
     */
    function arrayToXml($arr) {
        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
            if (is_numeric($val)) {
                $xml.="<".$key.">".$val."</".$key.">";
            } else {
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
}

if (!function_exists('xmlToArray')) {
    /*
     * xml转换数组
     */
    function xmlToArray($xml) {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
}

if (!function_exists('fun_curl_get')) {
    /**
     * json curl request
     * @param $url
     * @param $data
     * @param $token
     * @return mixed
     */
    function fun_curl_get($url){
        $ch = curl_init();
        //print_r($ch);
        curl_setopt( $ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时

        if(0 === strpos(strtolower($url), 'https'))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
        }
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }

}

if (!function_exists('fun_error_view')) {
    /**
     * json curl request
     * @param $url
     * @param $data
     * @param $token
     * @return mixed
     */
    function fun_error_view($code, $content, $url){
        if ($code == 0) {
            return view('admin.error')->with('info', ['error'=>$content, 'url'=>$url]);
        } else {
            return view('admin.error')->with('info', ['success'=>$content, 'url'=>$url]);
        }
    }

}


if (!function_exists('aes_decryptString')) {
    /**
     *  aes 解码手机号
     */
    function aes_decryptString($str,$key='827b35a782d85721e19b345c74343e9b'){
        $str = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
        $block = mcrypt_get_block_size('rijndael_128', 'ecb');
        $pad = ord($str[($len = strlen($str)) - 1]);
        $len = strlen($str);
        $pad = ord($str[$len-1]);
        $de_txt = substr($str, 0, strlen($str) - $pad);
        return  substr($de_txt, 0, 11);
    }
}

if (!function_exists('sendTemplate')) {
    /**
     *  发送模板消息
     */
    function sendTemplate($openid,$data,$template_id,$page='pages/home/home'){
        $formId = user_formid::lastformid($openid);
        if (!$formId) {
            savelog('no_push_user', 'openid=' . $openid);
            return 'exit';
        }
        $res = WxSmallClient::sendTemplate($openid, $data, $formId->form_id, $template_id, $page);
        if (!isset($res['errcode']) || $res['errcode'] != 0) {
            savelog('push_err_log', json_encode($res) . 'openid=' . $openid . '&form_id=' . $formId->form_id);
        }
        savelog('push_success_log', json_encode($res) . 'openid=' . $openid . '&form_id=' . $formId->form_id);
        // 删除用过的form_id
        user_formid::where('id', $formId->id)->delete();
    }
}




if (!function_exists('eachplue_curl')) {
    /**
     *  趋佳通用请求接口
     */
    function eachplue_curl($url, $body){
        //$url = env('EACH_URL').'/api/queryByUid';
        $sign_str = $url.json_encode($body).env('EACH_KEY');
        $sign = hash('sha256', $sign_str);
        $header = [
            'X-EACH-VENDOR-ID:'.env('EACH_VENDORID','106'),
            'X-EACH-APP-ID:'.env('EACH_APPID','points'),
            'X-EACH-SIGNATURE:'.$sign
        ];
        $start_time = round(microtime(true)*1000);
        $qujia_res = Curl::to( $url )
            ->withContentType('application/json')
            ->withHeader('X-EACH-VENDOR-ID: '.env('EACH_VENDORID','106'))
            ->withHeader('X-EACH-APP-ID: '.env('EACH_APPID','points'))
            ->withHeader('X-EACH-SIGNATURE: '.$sign)
            ->withData( $body )
            ->asJsonRequest()
            ->post();
        $end_time = round(microtime(true)*1000);
        $use_time = $end_time-$start_time;
        if ($use_time > 3000) {
            Storage::disk('public')->append('timeout_eachplue.log', 'time:'.date('Y-m-d H:i:s').'  use_time:'.$use_time.'  url:'.$url, '  params:'.json_encode($body));
        }
        $res = json_decode($qujia_res, true);
        return $res;
    }
}


if (!function_exists('savelog')) {
    function savelog($filename, $logdata)
    {
        // 文件路径 storage/app/log
        if (is_array($logdata)) {
            $logdata = json_encode($logdata, 320);
        }
        Storage::disk('logs')->append($filename, 'time:'.date('Y-m-d H:i:s')."\tresponse:".$logdata);
    }
}

if (!function_exists('fun_uinfo'))
{
    function fun_uinfo($unionid)
    {
        if (empty($unionid)) {
            return false;
        }
        $hash_key = 'uinfo_'.$unionid;
        $uinfo = Redis::get($hash_key);
        if ($uinfo) {
            return json_decode($uinfo, 1);
        } else {
            $uinfo = mcds_users::select('id', 'phone', 'nickname', 'wb_id', 'openid', 'unionid', 'avatar_url','send_time')
                ->where('unionid',$unionid)->first();
            if ($uinfo) {
                $uinfo = $uinfo->toArray();
                Redis::setex($hash_key, 7200, json_encode($uinfo));
                return $uinfo;
            } else {
                return false;
            }
        }
    }
}


/**
 * 获取本周开始和结束时间
 */
if (!function_exists('get_weektime'))
{
    function get_weektime()
    {
        $sdefaultDate = date("Y-m-d");
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w=date('w',strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w : 6).' days')).' 00:00:00';
//        $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - 1 : 6).' days')).' 00:00:00';
        //本周结束日期
        $week_end=date('Y-m-d',strtotime("$week_start +6 days")).' 23:59:59';
        return ['week_start'=>$week_start, 'week_end'=>$week_end];
    }
}

/**
 * 获取本周开始和结束时间  格式Ymd
 */
if (!function_exists('get_weekymd')) {
    function get_weekymd()
    {
        $sdefaultDate = date("Ymd");
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first = 1;
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date('w', strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start = date('Ymd', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));
        //本周结束日期
        $week_end = date('Ymd', strtotime("$week_start +6 days"));
        return [$week_start, $week_end];
    }
}


if (!function_exists('getNeedTime'))
{
    function getNeedTime()
    {
        $date=date('Y-m-d');  //当前日期
        $first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $now_start=date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $last_start=date('Y-m-d',strtotime("$now_start - 7 days"));  //上周开始日期
        $last_end=date('Y-m-d',strtotime("$now_start - 1 days"));  //上周结束日期
        $output['date'] = $date;
        $output['week_now_start'] = $now_start;
        $output['week_last_start'] = $last_start;
        $output['week_last_end'] = $last_end;

        //月环比
        $now_start = date("Y-m-d",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        $last_start =  date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
        $last_end =  date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y")));
        $output['month_now_start'] = $now_start;
        $output['month_last_start'] = $last_start;
        $output['month_last_end'] = $last_end;
        return $output;
    }
}

/**
 * 选手系统投票密码
 */
if (!function_exists('get_player_pass')) {
    function get_player_pass($player_id,$pass)
    {
        if($player_id <0 || $player_id > 100)
        {
            return false;
        }
        $playerPass = [
            '24'=>'123456',
            '2'=>'123456',
            '3'=>'123456',
            '4'=>'123456',
            '5'=>'123456',
            '6'=>'123456',
        ];
        if(isset($playerPass[$player_id]) &&  $playerPass[$player_id] == $pass)
        {
            return true;
        }
        return false;
    }
}

if (!function_exists('getFirstChart')) {
    function getFirstChart($str)
    {
        if (empty($str)) {
            return '';
        }
        $char = ord($str[0]);
        if ($char >= ord('A') && $char <= ord('z')) {
            return strtoupper($str[0]);
        }
        $s1 = iconv('UTF-8', 'GB2312//IGNORE',  $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return null;
    }
}
if (!function_exists('chartSort')) {
    function chartSort($user)
    {
        foreach ($user as $k => &$v) {
            $v['chart'] = getFirstChart($v['player_name']);
//                $v['player_name'] = iconv('UTF-8', 'GB2312//IGNORE',$v['player_name']);
        }
        $data = [];
        foreach ($user as $k => $v) {
            if (empty($data[$v['chart']])) {
                $data[$v['chart']] = [];
            }
            $data[$v['chart']][] = $v;
        }
        ksort($data);
        return $data;
    }
}

if (!function_exists('object_array')) {
    function object_array($array)
    {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_array($value);
        }
    }
        return $array;
    }
}

if (!function_exists('add_bonus_record')) {
    function add_bonus_record($openid,$before_bonus,$change_bonus,$after_bonus,$status,$type,$species)
    {
        $data['user_id'] = $openid;
        $data['before_bonus'] = $before_bonus;
        $data['change_bonus'] = $change_bonus;
        $data['after_bonus'] = $after_bonus;
        $data['status'] = $status;
        $data['type'] = $type;
        $data['species'] = $species;
        blessing_record::insert($data);
        Redis::del('blessing_'.$openid);
    }
}


if (!function_exists('postJsonSend')) {
    function postJsonSend($url, $data)
    {
        $a = round(microtime(true)*1000);
        $rs = Curl::to( $url )
            //->withOption('PORT', '8084')
            ->withTimeout(5)
            //->withContentType('application/json')
            ->withData( $data )
            ->asJsonRequest()
            ->post();
        $b = round(microtime(true)*1000);
        $c = $b-$a;
        if ($c >= 4000) {
            Storage::disk('crm_log')->append('time_out.log', 'time:'.date('Y-m-d H:i:s').'==>time_res'. $c .'==>respon:'.$rs.'  ==>arguments:'.$url.'==>'.json_encode($data));
        }
        $res = json_decode($rs, true);
        return $res;
    }
}

if (!function_exists('hashEncode')) {
    function hashEncode($id)
    {
        $hashids = new \Hashids\Hashids('jKCwLhTgqsq7lt/4=');
        return $hashids->encode($id);
    }
}

if (!function_exists('hashDecode')) {
    function hashDecode($id)
    {
        $hashids = new \Hashids\Hashids('jKCwLhTgqsq7lt/4=');
        return $hashids->decode($id);
    }
}

if (!function_exists('getPrizeNum')) {
    function getPrizeNum($key)
    {
        if ($key < 1) {
            return 500;
        } else if ($key < 6) {
            return 200;
        } else if ($key < 26) {
            return 100;
        }  else if ($key < 76) {
            return 50;
        }  else if ($key < 476) {
            return 10;
        } else {
            return 0;
        }
    }
}

if (!function_exists('getEncryption')) {
    function getEncryption($string)
    {
        $key = 'HAOYANGMAOSIMA12';
        return base64_encode(openssl_encrypt($string,"AES-128-ECB",$key,OPENSSL_RAW_DATA));
    }
}


if (!function_exists('getEncryptionKt5')) {
    function getEncryptionKt5($string, $pre_key)
    {
        $key = strtoupper($pre_key.'ta6cmZdr');
        //openssl_decrypt(base64_decode($string), "AES-128-ECB", $key, OPENSSL_RAW_DATA);
        return base64_encode(openssl_encrypt($string,"AES-128-ECB",$key,OPENSSL_RAW_DATA));
    }
}

if (!function_exists('uniqStr')) {
    function uniqStr()
    {
        $strs = str_shuffle("QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm");
        $key = substr($strs, 0, 8);
        return $key;
    }
}



if (!function_exists('getFavor')) {
    function getFavor($unionid)
    {
        if (empty($unionid)) {
            return false;
        }
        $hashKey = "favor_".$unionid;
        $favor = Redis::get($hashKey);
        if ($favor) {
            return true;
        } else {
            $url = "https://antacnwechat.anta.cn/amp/Message/storemem?union_id=".$unionid;
            $rs = Curl::to($url)->get();
            $rs = json_decode($rs, true);
            if (isset($rs['status']) && $rs['status'] == 200) {
                Redis::set($hashKey, 1);
                return true;
            }
            return false;
        }
    }
}

if (!function_exists('checkIp')) {
    function checkIp($hashKey, $number, $time=3600){
        $num = Redis::get('ip_'.$hashKey);
        $newNum = (int) $num + 1;
        Redis::setex($hashKey, $time, $newNum);
        if ((int) $newNum > $number) {
            return false;
        }
        return true;
    }
}

if (!function_exists('formDate')) {
    function formDate($date){
        return date("Y/m/d H:i:s", strtotime($date));
    }
}

if (!function_exists('getRand')) {
    function getRand($proArr) {
        static $arr = array();
        $key = md5(serialize($proArr));
        if (!isset($arr[$key])) {
            $max = array_sum($proArr);
            foreach ($proArr as $k=>$v) {
                $v = $v / $max * 10000;
                for ($i=0; $i<$v; $i++) $arr[$key][] = $k;//var_dump($arr);
            }
        }
        return $arr[$key][mt_rand(0,count($arr[$key])-1)];
    }
}

if (!function_exists('getPk')) {
    function getPk($now, $before) {
        if ($now == 0 && $before == 0) {
            $pk = 0;
        } elseif($now == 0) {
            $pk = -100;
        } else {
            $pk =(($now-$before)/$now)*100;
        }
        return sprintf("%.2f", $pk);
    }
}

if (!function_exists('todayOverTime')) {
    function todayOverTime()
    {
        return strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))) - time();
    }
}

if (!function_exists('do_throw')) {
    function do_throw($ret, $msg='')
    {
        if (!$ret) {
            throw new \Exception($msg ? $msg : '系统繁忙，请稍后在试！');
        }
    }
}


if (!function_exists('check_ip')) {
    function check_ip($ip, $key, $number=80)
    {
        if (!$ip) {
            fun_respon(0, '网络异常，请稍后在试！！！');
        }
        $hash = 'ips_'.$ip;
        $ipNum = Redis::get($hash);
        if ($ipNum >= $number) {
            savelog('check_ip_'.$key, $ip);
            fun_respon(0, '网络异常，请稍后在试！');
        } else {
            Redis::setex($hash, 60*30, (int) $ipNum + 1);
        }
    }
}

if (!function_exists('get_card_url')) {
    function get_card_url($uid, $gid, $mtype)
    {
        return '&uid='.$uid.'&gid='.$gid.'&m_type='.$mtype;
    }
}

if (!function_exists('docheck')) {
    function docheck($param)
    {
        if ($param === false || empty($param)) {
            fun_respon(0, '服务繁忙，请稍后再试！');
        }
    }
}

if (!function_exists('do_die')) {
    //取代if判断直接die退出
    function do_die($ret, $msg)
    {
        if ($ret) {
            fun_respon(0, $msg);
        }
    }
}

if (!function_exists('log_tag')) {
    function log_tag($ret, $file, $logs)
    {
        if (!$ret) {
            savelog($file, $logs);
        }
    }
}













