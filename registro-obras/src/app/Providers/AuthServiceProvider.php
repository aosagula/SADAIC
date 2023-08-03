<?php

namespace App\Providers;

use App\Providers\MemberProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        $this->app->auth->provider('custom', function ($app, array $config) {
            return new MemberProvider($app['hash'], $config['model']);
        });
    }
}
