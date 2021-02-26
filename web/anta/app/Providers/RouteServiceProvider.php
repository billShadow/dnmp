<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapRunRoutes(); //暂停服务
        $this->mapAntaRoutes();
        $this->mapDrawRoutes();
        $this->mapAuctionRoutes();
        $this->mapVipRoutes();
        $this->mapKtRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }


    /**
     * run route
     */
    protected function mapRunRoutes()
    {
        Route::prefix('run')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/run.php'));
    }

    /**
     * anta route
     */
    protected function mapAntaRoutes()
    {
        Route::prefix('anta')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/anta.php'));
    }

    /**
     * anta route
     */
    protected function mapDrawRoutes()
    {
        Route::prefix('draw')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/draw.php'));
    }

    /**
     * anta route
     */
    protected function mapAuctionRoutes()
    {
        Route::prefix('auction')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/auction.php'));
    }

    /**
     * anta route
     */
    protected function mapVipRoutes()
    {
        Route::prefix('vip')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/vip.php'));
    }

    protected function mapKtRoutes()
    {
        Route::prefix('kt')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/kt.php'));
    }
}
