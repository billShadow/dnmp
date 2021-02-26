<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Hashids\Hashids;

class HashidProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton("hashid", function (){
            return new Hashids();
        });
        $this->app->bind("hashid", function(){
            return new Hashids();
        });
    }
}
