<?php

namespace App\Console\Commands;

use App\Libs\AntaAppClient;
use App\Models\Team11\team11_activity;
use App\Models\Team11\team11_get_card;
use App\Models\Team11\team11_order_mid;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\Facades\Curl;

class MakeCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'makes:qrcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        //执行实时任务处理发放卡券操作
        //创建一个子进程
        $pid = pcntl_fork();

        if ($pid == -1) {
            throw new Exception('fork子进程失败');
        } elseif ($pid > 0) {
            //父进程退出,子进程变成孤儿进程被1号进程收养，进程脱离终端
            exit(0);
        }

        //创建一个新的会话，脱离终端控制，更改子进程为组长进程
        $sid = posix_setsid();
        if ($sid == -1) {
            throw new Exception('setsid fail');
        }

        //修改当前进程的工作目录，由于子进程会继承父进程的工作目录，修改工作目录以释放对父进程工作目录的占用。
        chdir('/');

        /**
         * 通过上一步，我们创建了一个新的会话组长，进程组长，且脱离了终端，但是会话组长可以申请重新打开一个终端，为了避免
         * 这种情况，我们再次创建一个子进程，并退出当前进程，这样运行的进程就不再是会话组长。
         */
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new Exception('fork子进程失败');
        } elseif ($pid > 0) {
            //再一次退出父进程，子进程成为最终的守护进程
            exit(0);
        }
        //由于守护进程用不到标准输入输出，关闭标准输入，输出，错误输出描述符
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);

        while(true) {
            $cardInfo = Redis::rpop(QUEUE_UNIS);
            if ($cardInfo) {
                $param = json_decode($cardInfo, true);
                savelog('team_make', $param);
                $orderInfo = team11_order_mid::where('id', $param['oid'])->first();
                if (!$orderInfo) {
                    continue;
                }
                if ($orderInfo['is_get'] == 1) {
                    continue;
                }
                $activity = team11_activity::getOne($param['activity_id']);
                $ret1 = AntaAppClient::getTicketUnionid($param['unionid'], $activity['code'], $activity['code_type'], 1);
                $ret2 = AntaAppClient::getTicketUnionid($param['unionid'], $activity['free_code'], $activity['free_code_type'], empty($param['num'])?1:$param['num']);
                if ((isset($ret1['status']) && $ret1['status'] == 1) || (isset($ret2['status']) && $ret2['status'] == 1)) {
                    //领取成功，修改状态
                    team11_order_mid::where('id', $param['oid'])->update(['is_get'=>1]);
                    team11_get_card::insert(['activity_id'=>$param['activity_id'], 'user_id'=>$param['uid'], 'result'=>json_encode([$ret1,$ret2], 320)]);
                } else {
                    //领取失败，重新加入队列
                    savelog('team_make_repeat', $cardInfo);
                    Redis::lpush(QUEUE_UNIS, $cardInfo);
                }
            }
        }
    }
}
