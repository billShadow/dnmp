<?php

namespace App\Jobs;

use App\Models\mcds_adm_user;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $param;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($param)
    {
        $this->param = $param;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(1);
        $res = json_decode($this->param, true);
        if ($res) {
            mcds_adm_user::add(['phone'=>$res['phone'], 'pass'=>$res['pass'], 'nickname'=>$res['nickname']]);
        }
        $this->delete();
    }
}
