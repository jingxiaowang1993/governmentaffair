<?php

namespace Government\Affair;

use Government\Affair\Classes\User;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(){
        $this->publishes([
            __DIR__.'/config/governmentaffair.php' => config_path('governmentaffair.php'),
        ]);
    }

    protected $defer = true;

    public function register()
    {
        $this->app->singleton(User::class,function(){
            return new User();
        });

        $this->app->alias(User::class, 'GovernmentAffairUser');
    }

    public function provides()
    {
        return [User::class];
    }
}