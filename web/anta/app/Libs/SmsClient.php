<?php
/**
 * 短信发送
 *  传参： GET
 *  示例： http://api.sendmomentum.com:9001/LeoApi2/Services.aspx?action=操作类型&apikey=本站用户名&mobile=手机号码&content=短信内容&hashcode=校验码
 *        hashcode = key+secret+mobile做的md5
 *  返回： String  1&errid=1&balance=2582&msg=全部发送成功
 *         errid 字典
 *          1 ： 发送成功
 *          0 ： 系统原因失败
 *         -1 :  hashcode或密码不正确
 *         -2 ： 接收号码不正确
 *         -3 :  内容为空或超长
 *         -4 :  内容含非法字符
 *         -6 ： 帐户余额不足
 */
namespace App\Libs;

use Ixudra\Curl\Facades\Curl;

class SmsClient
{
    private static $SMS_INTERFACE = 'http://api.sendmomentum.com:9001/sms.aspx';
    private static $SMS_USERNAME  = 'maa6ae19e3c14934d8530fc4';
    private static $SMS_PASSWORD  = 'mdbd0824bdac70ef2b9330a5';
    private static $SMS_ACTION    = 'send';      // action目前只有send

    /**
     * 短信发送
     * @param string $mobile  目的手机号码（多个手机号请用半角逗号或封号隔开）
     * @param string $content 短信内容
     * @return Mixed
     */
    public static function send($mobile, $content)
    {
        //  亲爱的麦当劳会员，您正在验证手机号码，验证码为XXX（5分钟内有效)。热线XXXX
        $HashCode = md5(self::$SMS_USERNAME . self::$SMS_PASSWORD . $mobile);
        $resp = Curl::to(self::$SMS_INTERFACE)
            ->withData([
                'action' => self::$SMS_ACTION,
                'apikey' => self::$SMS_USERNAME,
                'mobile' => $mobile,
                'content' => $content,
                'hashcode' => $HashCode,
            ])
            ->get();
        return $resp;
    }
}