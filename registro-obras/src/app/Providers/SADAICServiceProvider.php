<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\SADAIC\Integration;

class SADAICServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Integration::class, function ($app) {
            return new Integration();
        });
    }
}
