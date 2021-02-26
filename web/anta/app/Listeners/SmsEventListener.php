<?php

namespace App\Listeners;

use App\Events\SmsEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Libs\SmsClient;
use Illuminate\Support\Facades\Log;

class SmsEventListener
{
    private static $template = [
        //to用户：注册短信
        '1' => '欢迎注册麦当劳会员，验证码为%s（5分钟内有效), 请完成注册。热线XXXX。',
    ];

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SmsEvent  $event
     * @return void
     */
    public function handle(SmsEvent $event)
    {
        $template = static::$template[$event->type];
        if (is_array($event->arguments)) {
            $message = vsprintf($template, $event->arguments);
            if (env('SMS_SERVICE', false)) {
                $result = SmsClient::send($event->mobile, $message);
                var_dump($result);die;
            }
            //Log::notice($message, $event->mobile);
        }
    }
}
