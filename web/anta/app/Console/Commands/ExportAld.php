<?php

namespace App\Console\Commands;

use App\Libs\WeChat\WxSmallClient;
use App\Models\user_formid;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportAld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:exportald';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ald data export';

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
        try {
            $where = [];
            $where['order_info.status'] = 2;
            $where['goods.goods_type'] = 2;
            $data = order_info::leftjoin('goods', "order_info.goods_id", "=", "goods.id")->where($where)->orderBy('order_info.id','DESC')->select('order_info.id','order_number','goods_id','price_sum','user_id','create_time','order_status','title','original_price','retail_price','discount_price','goods_num','goods_type','goods_cover','card_id')->get();
            if($data){
                $template_id = '3Mx8M_1-0d9LykW5dWvhp2-NKqFUeckf11YzyTLtUr4';
                //示例数据根据消息模板填充
                $data = array(
                    'keyword3'=>array('value'=>'您有一张已购买的优惠券还未领取，请点击消息打开小程序领取','color'=>'#7167ce'),
                    'keyword4'=>array('value'=>'本宫PavoMea','color'=>'#7167ce'),
                );
                foreach ($data as $v) {
                    $data['keyword1']=array('value'=>$v['create_time'],'color'=>'#7167ce');
                    $data['keyword1']=array('value'=>$v['title'],'color'=>'#7167ce');


                    $formId = user_formid::lastformid($v['user_id']);
                    if (!$formId) {
                        savelog('no_push_user', 'openid='.$v['user_id'].'&order_id='.$v['id']);
                    }

                    $res = WxSmallClient::sendTemplate($user->openid, $data, $formId->form_id, $template_id);
                    if (!isset($res['errcode']) || $res['errcode'] != 0) {
                        savelog('push_err_log', json_encode($res).'openid='.$user->openid.'&form_id='.$formId->form_id);
                    }
                    // 删除用过的form_id
                    user_formid::where('id', $formId->id)->delete();

                }
            }
        } catch (\Exception $ex) {
            savelog('export_ald_log',  $ex->getMessage().$ex->getLine());
        }
    }
}
