<?php

namespace Altanay\WebexSms\Facades;

use Illuminate\Support\Facades\Facade;

class WebexSms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'altanay.webex-sms';
    }
}