<?php

namespace App\Console\Commands;

use App\Models\lucky_prize_record;
use App\Models\sign_record;
use App\Models\user;
use App\Models\user_cards;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Send extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:sends';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sends';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            while (true){

                static $page = 0;
                $pageSize = 7000;
                $prize = user_cards::where(['user_id'=>228050,'status'=>3])->orderBy('id','ASC')->skip($page)->take($pageSize)->get()->toArray();
                if(!$prize) break;
                $page = $pageSize+$page;
                foreach ($prize as $value){
                    DB::beginTransaction();
                    $res = user_cards::where(['user_id'=>$value['presenter'],'card_serial_number'=>$value['card_serial_number']])->update(['status'=>1]);
                    if(!$res){
                        savelog('card_fail',"修改失败--".$value['card_serial_number'].'--'.$value['presenter']);
                    }
                    $res = user_cards::where(['user_id'=>$value['user_id'],'card_serial_number'=>$value['card_serial_number']])->update(['status'=>5]);
                    DB::commit();
                    savelog('user_cards',$value['card_serial_number']);
                    echo '成功';
                }
            }
        }catch (\Exception $e){
            DB::rollback();
            var_dump($e->getMessage().$e->getLine());
            savelog('card_prize',$e->getMessage().$e->getLine());
        }

    }


    private function addBonus($uid,$bonus,$status){
        $blessing_num = user::getUserBlessing($uid);
        //添加祝福
        //扣减积分
        $before_bonus = $blessing_num;
        $change_bonus = $bonus;
        $after_bonus = $before_bonus-$change_bonus;
        //$status = $status;
        $type = 2;
        user::where(['id'=>$uid])->update(['blessing_num'=>$after_bonus]);
        add_bonus_record($uid,$before_bonus,$change_bonus,$after_bonus,$status,$type,1);
    }

    private function sendNoJoin(){
        $time = time();
        $today_time = strtotime(date('Y-m-d',$time))+12*60*60;
        $yesterday_time = strtotime(date('Y-m-d',$time))-12*60*60;
        //查询参与抽奖的用户
        $prize = lucky_prize_record::leftjoin('user','user.id','=','lucky_prize_record.user_id')->where('created_at', '>=',date('Y-m-d H:i:s',$yesterday_time))->where('created_at','<=',date('Y-m-d H:i:s',$today_time))->get()->toArray();
        $ids = array_column($prize,'user_id');
        $res = user::whereNotIn('id',$ids)->get()->toArray();
        $md = date('md',time());
        $key = 'push_list_no_prize_'.$md;
        $keys = 'push_user_no_prizes_'.$md;
        foreach ($res as $v){
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



    }

    private function sendJoin(){
        $time = time();
        $today_time = strtotime(date('Y-m-d',$time))+12*60*60;
        $yesterday_time = strtotime(date('Y-m-d',$time))-12*60*60;
        $prize = lucky_prize_record::leftjoin('user','user.id','=','lucky_prize_record.user_id')->where('created_at', '>=',date('Y-m-d H:i:s',$yesterday_time))->where('created_at','<=',date('Y-m-d H:i:s',$today_time))->get()->toArray();
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



    }






















}
