<?php

namespace App\Jobs;

use App\Models\mcds_phone_msg;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class TestList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $info;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $len = Redis::llen('test');
        for($len; $len>0; $len--) {
            $res = Redis::rpop('test');
            $info = json_decode($res, true);
            $data = [
                'phone'=>$info['phone'],
                'code'=>$info['code'],
            ];
            mcds_phone_msg::add($data);
        }

        /*$info = json_decode($this->info, true);
            $data = [
                'phone'=>$info['phone'],
                'code'=>$info['code'],
            ];
        mcds_phone_msg::add($data);*/
    }
}
