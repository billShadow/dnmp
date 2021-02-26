<?php

namespace App\Console;

use App\Http\Controllers\Anta\UserController;
use App\Http\Controllers\Vip\UserController as vipUser;
use App\Libs\AntaAppClient;
use App\Libs\SmsClient;
use App\Libs\WeChat\AntaWxSmallClient;
use App\Libs\WeChat\AuctionWxSmallClient;
use App\Libs\WeChat\CodeClient;
use App\Libs\WeChat\DrawWxSmallClient;
use App\Libs\WeChat\Kt6WxSmallClient;
use App\Libs\WeChat\RunWxSmallClient;
use App\Libs\WeChat\Team11WxSmallClient;
use App\Libs\WeChat\WxSmallClient;
use App\Libs\WeChat\WxSmsClient;
use App\Models\anta_formid;
use App\Models\anta_reward;
use App\Models\anta_share_history;
use App\Models\anta_user;
use App\Models\Auction\auction_follow;
use App\Models\Auction\auction_goods;
use App\Models\Auction\auction_join;
use App\Models\Auction\auction_money_history;
use App\Models\Auction\auction_share;
use App\Models\Auction\auction_user;
use App\Models\boy_record;
use App\Models\boy_user;
use App\Models\draw_follow;
use App\Models\goods;
use App\Models\Kt6\kt6_user;
use App\Models\lucky_prize_record;
use App\Models\run_energy_history;
use App\Models\run_rank_prize;
use App\Models\run_share_history;
use App\Models\run_user;
use App\Models\run_user_formid;
use App\Models\Team11\team11_activity;
use App\Models\Team11\team11_order_mid;
use App\Models\user;
use App\Models\user_channel_record;
use App\Models\user_formid;
use App\Models\user_prizes;
use App\Models\Vip\vip_card_history;
use App\Models\Vip\vip_goods;
use App\Models\Vip\vip_queue_order;
use App\Models\Vip\vip_user;
use App\Models\Vip\vip_level_data;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\Facades\Curl;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\MakeCode',
        'App\Console\Commands\ExportAld',
        'App\Console\Commands\Send'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 龙珠发模板消息
        /*$schedule->call(function () {
            // 龙珠发送模板消息
            $template_id = 'GyMzk8HuZh42z5NPEmUF1LzAZ31gYt0scq5rY1r0yFA';
            $pages = "pages/home/home";
            Storage::disk('logs')->append('do_user_formid_use2','start');
            $data = array(
                'keyword1'=>array('value'=>'安踏11.11福利大放送','color'=>'#7167ce'),
                'keyword2'=>array('value'=>"安踏11.11福利限时返场，比那一刻还低，手慢无",'color'=>'#7167ce'),
                //'keyword2'=>array('value'=>"KT5限量配色水晶球上线，依然免费送，速速来拿",'color'=>'#7167ce'),
            );
            $useArr = [];
            $list = user_formid::where([])->orderBy('id', 'desc')->groupBy('openid')->get(['id', 'openid', 'form_id'])->toArray();
            foreach ($list as $val) {
                $res = WxSmallClient::sendTemplate($val['openid'], $data, $val['form_id'], $template_id, $pages);
                $useArr[] = $val['id'];
                Storage::disk('logs')->append('do_user_formid_use',json_encode($res,320));
            }
            if (count($useArr) >= 1) {
                user_formid::whereIn('id', $useArr)->delete();
            }
            Storage::disk('logs')->append('user_formid_use2',json_encode($useArr));
        })->dailyAt('10:58');*/

        // 津贴小程序推送模板
        /*$schedule->call(function () {
            // 龙珠发送模板消息
            Storage::disk('logs')->append('do_anta_formid_use','start');
            $template_id = 'rGJpJ3MYw-f7g3yPvVsiHPIeLYO3A0V7QNFcI3CMZxk';
            $pages = "pages/home/home";
            $data = array(
                'keyword1'=>array('value'=>'安踏11.11福利大放送','color'=>'#7167ce'),
                'keyword2'=>array('value'=>"安踏11.11福利限时返场，比那一刻还低，手慢无",'color'=>'#7167ce'),
                //'keyword2'=>array('value'=>"KT5限量配色水晶球上线，依然免费送，速速来拿",'color'=>'#7167ce'),
            );
            $useArr = [];
            $list = anta_formid::where([])->orderBy('id', 'desc')->groupBy('openid')->get(['id', 'openid', 'form_id'])->toArray();
            foreach ($list as $val) {
                $res = AntaWxSmallClient::sendTemplate($val['openid'], $data, $val['form_id'], $template_id, $pages);
                Storage::disk('logs')->append('do_anta_formid_use2',json_encode($res, 320));
                $useArr[] = $val['id'];
            }
            if (count($useArr) >= 1) {
                anta_formid::whereIn('id', $useArr)->delete();
            }
            Storage::disk('logs')->append('anta_formid_use2',json_encode($useArr));
        })->dailyAt('11:37');*/


        //消息推送
        /*$schedule->call(function () {
            try{
                $time = time();
                //查询参与抽奖的用户
                $prize = user::where([])->get()->toArray();
                $md = date('md',time());
                $key = 'push_list_922'.$md;
                $keys = 'push_user_922'.$md;
                foreach ($prize as $v){
                    redis::lpush($key,json_encode($v));
                }
                $num = Redis::llen($key);
                $page = "pages/shop/shop";
                for ($i=0;$i<=$num;$i++){
                    $user_info = Redis::rpop($key);
                    $user_infos = json_decode($user_info,true);
                    $template_id = 'dkvoukZo0dxxoz_cyeMEGbm1MKWUwYRjZv43WMtiwms';
                    $data = array(
                        'keyword1'=>array('value'=>'安踏汤神送福利','color'=>'#7167ce'),
                        'keyword2'=>array('value'=>"KT卡即将回收，快去看看你能兑换什么",'color'=>'#7167ce'),
                    );
                    if(!Redis::sismember($keys,$user_infos['openid'])){
                        sendTemplate($user_infos['openid'],$data,$template_id,$page);
                        Redis::sadd($keys, $user_infos['openid']);
                        echo '成功';
                    }
                }
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        })->cron('00 11 30 9 * *');*/

        /*//参与抽奖的用户
        $schedule->call(function () {
            try {
                $time = time();
                $today_time = strtotime(date('Y-m-d',$time))+12*60*60;
                $yesterday_time = strtotime(date('Y-m-d',$time))-12*60*60;
                $prize = lucky_prize_record::leftjoin('user','user.id','=','lucky_prize_record.user_id')->where('created_at', '>=',date('Y-m-d H:i:s',$yesterday_time))->where('created_at','<=',date('Y-m-d H:i:s',$today_time))->get()->toArray();
                //var_dump($prize);exit;
                //$prize = user::where(['id'=>13])->get()->toArray();
                $md = date('md',time());
                $key = 'push_list_prize_'.$md;
                $keys = 'push_user_prizes_'.$md;
                foreach ($prize as $v){
                    redis::lpush($key,json_encode($v));
                }

                $num = Redis::llen($key);
                $page = "pages/home/activity/activity";
                for ($i=0;$i<=$num;$i++){
                    $user_info = Redis::rpop($key);
                    $user_infos = json_decode($user_info,true);
                    $template_id = 'JJC9vsi_fqiEDKcb6d3zmlOwL1zcOJjFutb89eZoY8Q';
                    $data = array(
                        'keyword1'=>array('value'=>'安踏新春送金喜','color'=>'#7167ce'),
                        'keyword2'=>array('value'=>"抽奖结果已公布，快看看【安踏纪念款大福千足金币】有没有入账哦",'color'=>'#7167ce'),
                    );
                    if(!Redis::sismember($keys,$user_infos['openid'])){
                        sendTemplate($user_infos['openid'],$data,$template_id,$page);
                        Redis::sadd($keys, $user_infos['openid']);
                        echo '成功';
                    }
                }
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        })->dailyAt('12:01');*/


        //参与抽奖的用户
        /*$schedule->call(function () {
            try {
                $ress = DB::select('SELECT channel_id,count(*) as num FROM `user_channel_record` GROUP BY channel_id');
                $res = array_map('get_object_vars', $ress);
                foreach ($res as $key=>&$v){
                    $datas = getnums($v['channel_id'],date('d'));
                    $v['people'] = $datas[0];
                    $v['today_num'] = $datas[1];

                    $data = getnums($v['channel_id'],date('d',strtotime('-1 day')));
                    $v['yes_people'] = $data[0];
                    $v['yes_num'] = $data[1];
                }
                $key = 'adm_watch_datas'.date('md');
                Redis::setex($key, 6*60*60, json_encode($res));
                savelog('channel_data_cron', date("Y-m-d H:i:s")."Succss".PHP_EOL);
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        })->everyMinute();
        function getnums($channel_id,$time){
            $num = 0;
            $today_num = user_channel_record::where(['channel_id'=>$channel_id])->whereDay('created_at', '=', $time)->get();
            foreach ($today_num as $item){
                if($channel_id > 10 && $channel_id<=20){
                    //查询对应的openid
                    $user = anta_user::GetFirstId($item['user_id']);
                    if($user){
                        $count = anta_share_history::getNum($user->openid);
                        $num += $count;
                    }
                }elseif($channel_id > 30 && $channel_id <=40){
                    $user = run_user::GetFirstId($item['user_id']);
                    if($user){
                        $count = run_share_history::getNum($user->openid);
                        $num += $count;
                    }
                }else{
                    $count = user::where(['invite_id'=>$item['user_id']])->count();
                    $num += $count;
                }
            }
            return [$num,count($today_num)];
        }
        */

        /*//每天抽出中奖用户
        $schedule->call(function () {
            try {
                $user_1 = user::where(['id'=>33427])->first();
                $user_2 = user::where('id','!=',33427)->where(['blessing_num'=>0])->where('avatarUrl','!=','')->inRandomOrder()->first();
                $user_3 = user::where('id','!=',33427)->inRandomOrder()->first();

                $add_data = [
                    'openid' => substr($user_1['openid'],2),
                    'unionid' => $user_1['unionid'],
                    'invite_id' => 0,
                    'gender' => 1,
                    'avatarUrl' => $user_2['avatarUrl'],
                    'nickName' => $user_3['nickName'],
                    'city' => '',
                    'country' => '',
                    'province' => '',
                    'ip' => '',
                    'blessing_num' => 0,
                ];
                $res = user::insertGetId($add_data);
                user_prizes::insertGetId(['user_id'=>$res,'created_at'=>date('Y-m-d H:i:s',time())]);
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        })->dailyAt('23:59:58');*/

        // 能量商城 每天的排行榜数据处理
        /*$schedule->call(function () {
            if (time() <= strtotime("2020-12-28 00:00:00")) {
                return false;
            }
            $date = date('Y-m-d', strtotime('-1 day'));
            $res = run_energy_history::select( array(DB::raw('SUM(num) as num_total'), 'run_energy_history.openid', 'run_user.avatarUrl','run_user.nickName'))
                ->leftJoin('run_user', 'run_user.openid', 'run_energy_history.openid')
                ->where('save_date', $date)
                ->groupBy('run_energy_history.openid')
                ->orderBy('num_total', 'desc')
                ->get()
                ->toArray();
            if (empty($res)) {
                Log::notice('[cront_rank] ranklist empty');
                return 0;
            }
            $openids = array_column($res, 'openid');
            $additionRes = run_energy_history::select( array(DB::raw('SUM(num_addition) as num_total'), 'run_energy_history.openid'))
                ->where('save_date', $date)
                ->whereIn('openid', $openids)
                ->where('isAddition', 1)
                ->groupBy('run_energy_history.openid')
                ->get()->toArray();
            $addtionOpenids = array_column($additionRes, 'openid');
            $additionList = array_combine($addtionOpenids, $additionRes);
            foreach ($res as $k=>&$v) {
                if (isset($additionList[$v['openid']])) {
                    $v['num_total'] = $v['num_total'] + $additionList[$v['openid']]['num_total'];
                }
            }
            usort($res, function($n, $m) {
                $n1 = $n['num_total'];
                $m1 = $m['num_total'];
                if ($n1 == $m1)
                    return 0;
                return ($n1 > $m1) ? -1 : 1;
            });

            $addDatas = [];
            foreach ($res as $key=>$val) {
                if ($key <= 475) { // 总共排名476个
                    $prize = getPrizeNum($key);
                    $addDatas[] = ['openid'=>$val['openid'], 'num'=>$val['num_total'],'prize'=>$prize, 'save_date'=>date('Y-m-d')];
                }
            }
            // 谨慎此操作 先判断下
            if (isset($addDatas[0]['openid'])) {
                $isRepeat = run_rank_prize::getTodayIsvalid($addDatas[0]['openid']);
                if (!empty($isRepeat)) {
                    Log::notice('[cront_rank]');
                    return 0;
                }
            }
            $res = run_rank_prize::insert($addDatas);
            if (empty($res)) {
                Log::notice('[cront_rank] '.$res);
            } else {
                Log::notice('[cront_rank] Success '.date('Y-m-d H:i:s'));
                return 1;
            }
        })->dailyAt('00:00:10');*/

        // 跑步能量小程序的 打卡模板发送
        /*$schedule->call(function () {
            $list = Redis::hgetall(RUN_HASH);
            $template_id = 'HiTX1mJvo8r0BpI4OQo91ZufL8gs6auXUM1sjwzM2JE';
            $pages = "pages/home/home";
            $data = array(
                'keyword1'=>array('value'=>'步数签到','color'=>'#7167ce'),
                'keyword2'=>array('value'=>"坚持运动，今日份奖励还未兑换哦",'color'=>'#7167ce'),
            );
            $timeMin3 = time()-4*24*60*60;
            $timeMax3 = time()-3*24*60*60;

            $timeMin6 = time()-7*24*60*60;
            $timeMax6 = time()-6*24*60*60;
            foreach ($list as $openid=>$timev) {
                if (($timev >= $timeMin3 && $timev <= $timeMax3) || ($timev >= $timeMin6 && $timev <= $timeMax6)) {
                    $sends[] = $openid;
                }
            }
            if (!empty($sends)) {
                $formids = run_user_formid::whereIn("openid", $sends)->orderBy('id', 'desc')->get(['id','openid', 'form_id'])->toArray();
                $ids = array_column($formids, "id");
                if (!empty($formids)) {
                    foreach ($formids as $val) {
                        $res = RunWxSmallClient::sendTemplate($val['openid'], $data, $val['form_id'], $template_id, $pages);
                        savelog('run_push_templete', $res);
                    }
                    $ret = run_user_formid::whereIn("id", $ids)->delete();
                    savelog('run_push_templete', "[push_ret] ".$ret);
                }
            }
            Log::notice('[cront_run_templ] Success '.date('Y-m-d H:i:s'));
        })->cron('1 20 * * *');*/

        // 每天删除已经过期的formid
        /*$schedule->call(function () {
            $delTime = date("Y-m-d H:i:s", strtotime("-7 day"));
            run_user_formid::where("created_time", "<=", $delTime)->delete();
            Log::notice('[cront_run_del_formid] Success '.date('Y-m-d H:i:s'));
        })->cron('1 23 * * *');*/


//********************************************** 我是分割线 ***********************************************
        // 津贴排行榜
        /*$schedule->call(function () {
            #第1 500元无门槛，第10，第20  300元无门槛，第30、40、50、60、70、80、90、100 100元无门槛，第101-500名 10元门槛
            $list = anta_user::orderByDesc('total')->orderBy('id')->limit(500)->get(['id','openid', 'nickName', 'avatarUrl', 'total'])->toArray();
            $data = [];
            foreach ($list as $k=>$v) {

                $item = ['openid'=>$v['openid']];
                $rank = $k+1;
                // debug
                if ($rank == 1) {
                    $item['rank'] = 1;
                    $urlStrP = '&uid='.$v['id'].'&gid=500';
                    $item['path'] = "pages/tool/voucher/voucher?type=coupon&promo_id=1982&uid=N5&gid=vZ&utm_source=nhjjt".$urlStrP;
                } elseif ($rank == 10 || $rank == 20) {
                    $urlStrP = '&uid='.$v['id'].'&gid=300';
                    $item['rank'] = $rank;
                    $item['path'] = "pages/tool/voucher/voucher?type=coupon&promo_id=1983&uid=N5&gid=vZ&utm_source=nhjjt".$urlStrP;
                } elseif (in_array($rank, [30,40,50,60,70,80,90,100])) {
                    $item['rank'] = $rank;
                    $urlStrP = '&uid='.$v['id'].'&gid=100';
                    $item['path'] = 'pages/tool/voucher/voucher?type=coupon&promo_id=1984&uid=N5&gid=vZ&utm_source=nhjjt'.$urlStrP;
                } elseif ($rank > 100) {
                    $item['rank'] = $rank;
                    $urlStrP = '&uid='.$v['id'].'&gid=10';
                    $item['path'] = 'pages/tool/voucher/voucher?type=coupon&promo_id=1985&uid=N5&gid=vZ&utm_source=nhjjt'.$urlStrP;
                }
                if (count($item) > 1) {
                    //$item['path'] = "pages/index/index";
                    $data[] = $item;
                }
            }
            $ret = anta_reward::insert($data);
            if (!$ret) {
                savelog('kernel_anta_rank.txt', '插入排行榜数据失败');
            } else {
                savelog('kernel_anta_rank.txt', 'success');
            }


        })->cron('50 19 2 1 *');*/








######################################## 抽签 start ########################################
        // 抽签活动的开始活动和结束活动的消息提示
        $schedule->call(function () {
            $res = false;
            $data = array(
                'thing11'=>array('value'=>'活动已经开始啦，快去抢安踏好礼')
            );
            $start = 'nMejg_-ZZiJestbsLDBi3ub0I7AMXLPuUojmVQvm1BQ';
            $date = date('Y-m-d H:i');
            $sql = 'SELECT * FROM `draw_goods` where DATE_FORMAT(start_time, "%Y-%m-%d %H:%i") = "'.$date.'"';
            $ret = DB::select($sql);
            if (count($ret) >= 1) {
                foreach ($ret as $item) {
                    $data['thing8'] = ['value'=>$item->name];
                    $data['date9'] = ['value'=>$item->start_time];
                    $data['date10'] = ['value'=>$item->end_time];
                    $list = draw_follow::where(['type'=>2, 'goods_id'=>$item->id])->get()->toArray();
                    savelog('draw_kernel', ['start_draw_follow', count($list)]);
                    foreach ($list as $v) {
                        $res = DrawWxSmallClient::sendMsg($v['openid'], $data, $start);
                    }
                }
            }
            if ($res) {
                #Log::notice('[draw_cront] start Success '.date('Y-m-d H:i:s').json_encode([$data, $res], 320));
            }
        })->cron('* * * * *');

        // 抽签活动的结束活动的消息提示
        $schedule->call(function () {
            $res = false;
            $data = array(
                'thing4'=>array('value'=>"活动已开奖，赶紧看看中奖结果啦！"),
            );
            $end = 'oZaOB_9Jde5MALUNp7rynrMiNbe6rKjTp2wpZ2-CVks';
            $date = date('Y-m-d H:i');
            $sql = 'SELECT * FROM `draw_goods` where DATE_FORMAT(end_time, "%Y-%m-%d %H:%i") = "'.$date.'"';
            $ret = DB::select($sql);
            if (count($ret) >=1) {
                foreach ($ret as $item) {
                    $data['thing1'] = ['value'=>'安踏抽签抢好礼'];
                    $data['thing5'] = ['value'=>$item->name];
                    $list = draw_follow::where(['type'=>1, 'goods_id'=>$item->id])->get()->toArray();
                    savelog('draw_kernel', ['end_draw_follow', count($list)]);
                    foreach ($list as $v) {
                        $res = DrawWxSmallClient::sendMsg($v['openid'], $data, $end);
                    }
                }

            }
        })->cron('* * * * *');

        //更新优惠券上架
        $schedule->call(function () {
            goods::whereIn('id', [111,112])->update(['is_valid'=>0]);
            goods::whereIn('id', [113,114,115])->update(['is_valid'=>0]);
        })->cron('00 00 01 5 *');
######################################## 抽签 end ########################################









######################################## 组团小程序计划任务 start ########################################
        //每秒钟处理redis的组团队列消息
        $schedule->call(function () {
            $key = UserController::SEND_QUEUE;
            $len = Redis::llen($key);
            $ret = 'success';
            #$tmpID = 'M2Mt310Qy2q92KtLDjZ-LIVp7WhSl1qt6A2Jnn9Iiks';
            $tmpID = 'BaZLGSbui0Gs3i9-HptdByVQx432dbOASiz4hL-PXpU';
            for ($i=1; $i<= $len; $i++) {
                $info = Redis::rpop($key);
                $info = json_decode($info, true);
                $data = array(
                    'thing2'=>array('value'=>'组队成功，优惠限量快去领取吧~')
                );
                $data['thing1'] = ['value'=>$info['name']];
                $ret = AntaWxSmallClient::sendMsg($info['openid'], $data, $tmpID);
            }
            savelog('boy_queue', $ret);
        })->cron('*/6 * * * *');


        //每天打卡提醒
        $schedule->call(function () {
            $tmpID = 'YCVNcLkgXjuC4BnWFjf_j3iWDnKKXSL4EicW1eW8jmE';
            $data = array(
                'thing5'=>array('value'=>'安踏至低优惠'),
                'thing3'=>array('value'=>'今天记得签到哦，限量优惠即将到手~')
            );
            $todayDo = boy_record::where('record_date', date('Y-m-d'))->get(['openid'])->toArray();
            $openids = array_column($todayDo, 'openid');
            $list = boy_user::all(['openid'])->toArray();
            foreach ($list as $v) {
                if (in_array($v['openid'], $openids)) {
                    continue;
                }
                $ret = AntaWxSmallClient::sendMsg($v['openid'], $data, $tmpID);
                savelog('boy_cron_record', $ret);
            }
        })->cron('0 20 * * *');
######################################## 组团小程序计划任务 end   ########################################









######################################## 会员中心小程序计划任务 start ########################################
        //统计昨天用户等级情况并记录到数据表中
        $schedule->call(function () {
            $ranks = [
                '1'=>['min'=>1, 'max'=>10],
                '2'=>['min'=>11, 'max'=>20],
                '3'=>['min'=>21, 'max'=>30],
                '4'=>['min'=>31, 'max'=>40],
                '5'=>['min'=>41, 'max'=>50],
                '6'=>['min'=>51, 'max'=>60],
                '7'=>['min'=>61, 'max'=>70],
                '8'=>['min'=>71, 'max'=>80],
                '9'=>['min'=>81, 'max'=>90],
            ];
            $adds = [];
            foreach ($ranks as $k=>$v) {
                $num = vip_user::where('level','>=', $v['min'])->where('level','<=', $v['max'])->count();
                $item = ['type'=>$k, 'num'=>$num, 'date'=>date('Y-m-d', strtotime('-1 day'))];
                $adds[] = $item;
            }
            vip_level_data::insert($adds);
        })->cron('0 0 * * *');

        //每分钟处理redis的发券队列
        $schedule->call(function () {
            $adds = [];
            $len = Redis::llen(AntaAppClient::PUSH_CARD);
            for ($i=1; $i<= $len; $i++) {
                $info = Redis::rpop(AntaAppClient::PUSH_CARD);
                $info = explode('@@', $info);
                $adds[] = ['openid'=>$info[0], 'code'=>$info[1]];
            }
            $ret = vip_card_history::insert($adds);
            if (!$ret) {
                savelog('kernel_vip', ['card_queue', $adds, $ret]);
            }
        })->cron('*/2 * * * *');

        //每五分钟处理未找到用户的订单推送数据，用户下单调用加金币接口未找到用户的数据入库
        $schedule->call(function () {
            $len = Redis::llen(vipUser::VIP_NOT_FOUND_USER_ORDER);
            $orderNos = vip_queue_order::get(['order_no'])->toArray();
            $orderNos = array_column($orderNos, 'order_no');
            $adds = $insertNos = [];
            for ($i=1; $i<= $len; $i++) {
                $info = Redis::rpop(vipUser::VIP_NOT_FOUND_USER_ORDER);
                $infoArr = json_decode($info, true);
                if (in_array($infoArr['order_no'], $insertNos)) {
                    continue;
                }
                if (in_array($infoArr['order_no'], $orderNos)) {
                    continue;
                }
                $insertNos[] = $infoArr['order_no'];
                $adds[] = ['openid'=>$infoArr['openid'], 'order_no'=>$infoArr['order_no'], 'info'=>$info];
            }
            if (!empty($adds)) {
                vip_queue_order::insert($adds);
            }
        })->cron('*/7 * * * *');

        //用户下单调用加金币接口未找到用户的数据的订单  重试调用加金币接口
        //todo 接口可能存在隐患==vip_queue_order数据越来越多之后全量取出可能造成数据阻塞情况
        $schedule->call(function () {
            $ret = vip_queue_order::where('is_valid', 1)->get(['id','info', 'openid'])->toArray();
            if (!$ret) {
                die('error');
            }
            $openids = array_column($ret, 'openid');
            $orders = array_combine($openids, $ret);
            if (!$openids) {
                die('error');
            }
            $users = vip_user::whereIn('openid', $openids)->get(['openid'])->toArray();
            if (empty($users)) die('error');

            foreach ($users as $v) {
                $info = $orders[$v['openid']]['info'];
                $list = Curl::to('https://anta.yokeneng.com/vip/api/userpay')
                    ->withData(json_decode($info, true))
                    ->get();
                $ret = json_decode($list, true);
                if ($ret['msg'] == '未找到该用户') {
                    savelog('xiaobin_1111', $info);
                    continue;
                }
                $ids[] = $orders[$v['openid']]['id'];
            }
            if (!empty($ids)) {
                vip_queue_order::whereIn('id', $ids)->update(['is_valid'=>0]);
            }
        })->cron('*/13 * * * *');


        // tag=invented_sku
        //虚拟库存处理【invented_sku字段】
        //逻辑：按照每天八点来更新库存
        $schedule->call(function () {
            $log = ['tag'=>"invented_sku", 'ret'=>'empty', 'ids'=>''];
            $list = vip_goods::where(['is_valid'=>1])->where('invented_sku', '>', 0)->get(['id','invented_sku'])->toArray();
//            $list = vip_goods::where(['is_valid'=>1])->where('invented_sku', 2)->get(['id','invented_sku'])->toArray();
            if (empty($list)) {
                savelog('kernel_vip', $log);
                return;
            }
            foreach ($list as $v) {
                $ret = vip_goods::where('id', $v['id'])->update(['num'=>$v['invented_sku']]);
                $log['ids'] = $log['ids']."-".$v['id'];
            }
            $log['ret'] = $ret;
            savelog('kernel_vip', $log);

        })->cron('0 20 * * *');

        $schedule->call(function () {
            savelog('kernel_vip', "测试发布时间问题");
        })->cron('32 20 * * *');
######################################## 会员中心小程序计划任务 end ########################################









######################################## 拍买 start ########################################
        //每分钟处理redis的分享人队列
//        $schedule->call(function () {
//            $hashkey = \App\Http\Controllers\Auction\UserController::SHARE_QUEUE;
//            $len = Redis::llen($hashkey);
//            for ($i=1; $i<= $len; $i++) {
//                $item = Redis::rpop($hashkey);
//                $info = explode('@@', $item);
//                $users = auction_user::findMany($info, ['id','openid','money'])->toArray();
//                if (count($users) != 2) {
//                    continue;
//                }
//                $todayShare = auction_share::todayShare($users[0]['openid']);
//                $sendMoney = rand(20,50);
//                if ($todayShare > 50) {
//                    $sendMoney = rand(1,5);
//                    savelog('auction_share_err', $item);
//                }
//                auction_share::insert(['send_money'=>$sendMoney, 'share_openid'=>$users[0]['openid'], 'openid'=>$users[1]['openid']]);
//                auction_user::inc($users[0]['openid'], $sendMoney);
//                auction_money_history::insert(['openid'=>$users[0]['openid'], 'money'=>$sendMoney, 'old_money'=>$users[0]['money'], 'type'=>2]);
//            }
//
//            savelog('kernel_auction', ['card_queue',date('Y-m-d H:i:s')]);
//        })->cron('* * * * *');

        //机器人逻辑，后台编辑拍卖活动可填写“最低成交价”（不填则默认为0），竞拍进行中时，当最新出价低于最低成交价，
        //机器人每30-120分钟会自动加价10-50拍拍币（出价机器人随机）；当用户出价高于最低成交价时，则机器人不参与拍卖
        //-当活动开始后半小时没人出价时，随机10-60分钟机器人会进行出价10拍拍币
        $schedule->call(function () {
            $list = auction_goods::startDraw();
            if ($list) {
                $userControl = new \App\Http\Controllers\Auction\UserController();
                foreach ($list as $v) {
                    $goods_id = $v['id'];
                    $hashKey = 'robo_'.$goods_id;
                    $doit = Redis::get($hashKey);
                    if ($doit) {
                        continue;
                    }
                    $between = (strtotime($v['end_time'])-strtotime($v['start_time']))/60;
                    $maxMoney = intval(auction_join::where('goods_id', $goods_id)->max('money'));

                    //活动时间基本固定在四个小时。按照四个小时平均分配
                    //$secend = 60*rand(10,20); // 10-20分钟参加一次
                    //$randMoney = rand(5,40);

                    $secend = 60*rand(4,8);
                    $randMoney = ($v['min_money']/($between/9));
                    $randMoney = rand($randMoney,$randMoney+30);
                    savelog('xiaobin_1001', [$between, $goods_id, $randMoney]);

                    /*if ($v['min_money'] > 500) {
                        $secend = 60*rand(3,10); //3-10分钟参加一次
                        $randMoney = rand(30,50);
                    } elseif ($v['min_money'] > 1000) {
                        $secend = 60*rand(5,10); //3-10分钟参加一次
                        $randMoney = rand(40,80);
                    }*/
                    Redis::setex($hashKey, $secend,1);
                    $userControl->roboJoin($goods_id, $randMoney, $maxMoney);
                }

            }
        })->everyFiveMinutes();

        //活动结束，退还拍卖失败的用户拍拍币
        $schedule->call(function () {
            $date = date('Y-m-d H:i:s');
            $list = auction_goods::where(['is_return'=>0])->where("end_time", '<', $date)->get(['id'])->toArray();
            foreach ($list as $v) {
                $add = [];
                $joins = auction_join::where('goods_id', $v['id'])->orderByDesc('money')->get(['openid', 'money'])->toArray();
                foreach ($joins as $key => $join) {
                    if ($key == 0 || in_array($join['openid'],\App\Http\Controllers\Auction\UserController::robots)) {
                        continue;
                    }
                    $uinfo = auction_user::getOne($join['openid'], ['money','id']);
                    $add[] = ['openid'=>$join['openid'], 'money'=>$join['money'], 'old_money'=>$uinfo['money'], 'type'=>auction_money_history::TYPE3];
                    auction_user::incId($uinfo['id'], $join['money']);
                }
                auction_money_history::insert($add);
                auction_goods::where('id', $v['id'])->update(['is_return'=>1]);
            }
            savelog('kernel_auction', ['end_return',date('Y-m-d H:i:s')]);
        })->everyFiveMinutes();

        // 拍卖活动的开始活动消息提示
        $schedule->call(function () {
            $res = false;
            $data = array(
                'thing11'=>array('value'=>'竞拍已经开始，切勿错过哦')
            );
            $start = 'Xok7BEFKB2NZ_jBpNqTbDzE3qf8Zvb5TvWABq2kfWeM';
            $date = date('Y-m-d H:i');
            $sql = 'SELECT * FROM `auction_goods` where DATE_FORMAT(start_time, "%Y-%m-%d %H:%i") = "'.$date.'"';
            $ret = DB::select($sql);
            if (count($ret) >= 1) {
                savelog('auction_msg', ["start_goods_num", $ret]);
                $list  = auction_user::where('id', '>', 20)->where('money', '>', 200)->get(['openid'])->toArray();
                foreach ($ret as $item) {
                    $data['thing1'] = ['value'=>$item->name .'竞拍'];
                    $data['date5'] = ['value'=>date("H:i", strtotime($item->start_time))];
                    foreach ($list as $v) {
                        $res = AuctionWxSmallClient::sendMsg($v['openid'], $data, $start);
                    }
                    savelog('auction_msg', " counts=".count($list));
                }
                Log::notice('[auction_cront] start Success '.date('Y-m-d H:i:s').json_encode([$data, $res], 320));
            }
        })->cron('*/3 * * * *');

        //活动即将结束提醒 -- 结束前五分钟内提醒
        $schedule->call(function () {
            $res = false;
            $data = array(
                'thing3'=>array('value'=>"最后五分钟，出价激烈角逐中，快去看看哦"),
            );
            $end = 'JDQtRllxD58nD9pUjXcWIlWESwEPswjt6X2dePnM64g';
            $date = date('Y-m-d H:i', time() + 5*60);
            $sql = 'SELECT * FROM `auction_goods` where DATE_FORMAT(end_time, "%Y-%m-%d %H:%i") = "'.$date.'"';
            $ret = DB::select($sql);
            if (count($ret) >=1) {
                foreach ($ret as $item) {
                    $data['thing1'] = ['value'=>$item->name .'竞拍'];
                    $data['time2'] = ['value'=>$item->end_time];
                    $list = auction_follow::where(['type'=>1, 'goods_id'=>$item->id])->get()->toArray();
                    savelog('auction_msg', ['end_follow', count($list)]);
                    foreach ($list as $v) {
                        $res = AuctionWxSmallClient::sendMsg($v['openid'], $data, $end);
                    }
                }
                Log::notice('[auction_cront] end Success '.date('Y-m-d H:i:s').json_encode([$data, $res], 320));
            }

        })->cron('*/2 * * * *');




######################################## 拍买 end   ########################################









######################################## KT6 start ########################################
        $schedule->call(function () {
            $tmpID = 'qNTIyac8risfdnr-fRBq1m011MdCC-ucHM6VZCqjcxI';
            $data = array(
                'thing8'=>array('value'=>'开盒机会即将过期'),
                'thing10'=>array('value'=>'今日开盒机会即将过期，快去看看')
            );
            $list = [];//kt6_user::all(['openid'])->toArray();
            foreach ($list as $v) {
                //$ret = Kt6WxSmallClient::sendMsg($v['openid'], $data, $tmpID, 'pages/index/index');
                //savelog('kt6', '[kernel] :'.json_encode($ret, 320));
            }
        })->cron('0 22 * * *');
######################################## KT6 end  ########################################











######################################## team11 start ########################################
        //每天十一点四十检测一下活动，将活动标记为结束
        $schedule->call(function () {
            $ret = team11_activity::updateActOverType();
            savelog('team11_kernel', $ret);
        })->cron('*/5 * * * *');


        //机器人自动参加活动
        /**
            前30%时间：进度70%
            30%-70%时间：进度90%
            70%-90%时间：进度100%
            （不受实际参与人数影响，比如实际参与人数100%，那加上机器人最终参与人数就有200%）
         */
        $schedule->call(function () {
            $time = time();
            $ret = team11_activity::getCurentRun();
            foreach ($ret as $v) {
                $joins = team11_order_mid::getJoinUserNum($v['id']);
                if ($joins > ($v['need_mans'] + ($v['need_mans'] * rand(1,30)/100))) {
                    continue;
                }
                $st = strtotime($v['start_time']);
                $et = strtotime($v['end_time']);
                $between = $et - $st;
                $thirty = $st  + $between * 0.3;
                $seventy = $st  + $between * 0.7;
                $eighty = $st  + $between * 0.9;
                if ($time >= $st && $time <= $thirty) {
                    $rand = ceil(($v['need_mans']*0.7)/(($thirty-$st)/300)) ;
                } elseif ($time > $thirty && $time <= $seventy) {
                    $rand = ceil(($v['need_mans']*0.2)/(($seventy-$thirty)/300)); //90%-70%=20%
                } elseif ($time > $seventy && $time <= $eighty) {
                    $rand = ceil(($v['need_mans']*0.1)/(($eighty-$seventy)/300)) ;
                } else {
                    continue;
                }
                if ($rand < 1) {
                    continue;
                }
                $rand  = $rand + rand(0,1);
                $addData = [];
                for ($rand;$rand > 0; $rand--) {
                    $addData[] = ['activity_id'=>$v['id'],'user_id'=>0, 'order_no'=>'0','status'=>2];
                }
                $ret = team11_order_mid::insert($addData);
                savelog('team11_kernel', $ret);
            }
        })->cron('*/10 * * * *');

        //每十分钟检测活动是否满员需要推送消息
        $schedule->call(function () {
            $tmpID = 'TLqpaOWPGJttYQn4OGzsi1kbdoP7uDOXjDBykxKYGWQ';
            $data = array(
                'thing1'=>array('value'=>'超级团'),
                'thing2'=>array('value'=>'组团成功'),
                'thing4'=>array('value'=>'快来领取专属优惠券')
            );
            $list = team11_activity::getRunAct();
            $sendIds = Redis::smembers('team11_send_act_ids');
            foreach ($list as $v) {
                if (in_array($v['id'], $sendIds)) {
                    continue;
                }
                $count = team11_order_mid::joinUserCount($v['id']);
                if ($count >= $v['need_mans']) {
                    //组团成功发送消息
                    Redis::sadd('team11_send_act_ids', $v['id']);
                    $openids = team11_order_mid::userOpenids($v['id'], $v['need_mans']);
                    if (!$openids) {
                        continue;
                    }
                    $data['thing1'] = ['value'=>$v['goods_name'].'超级团'];
                    foreach ($openids as $openid) {
                        $ret = Team11WxSmallClient::sendMsg($openid, $data, $tmpID);
                        savelog('kernel_team11', [$ret, $openid]);
                    }
                }

            }
        })->cron('*/10 * * * *');
######################################## team11 end  ########################################

















    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
