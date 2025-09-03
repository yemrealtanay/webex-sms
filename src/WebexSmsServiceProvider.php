<?php

namespace Altanay\WebexSms;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class WebexSmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/webex-sms.php', 'webex-sms');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/webex-sms.php' => config_path('webex-sms.php'),
        ], 'webex-sms-config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->app->singleton('altanay.webex-sms', function() {
            return new \Altanay\WebexSms\Services\Client();
        });
    }
}