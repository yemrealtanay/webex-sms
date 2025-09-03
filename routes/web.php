<?php
use Illuminate\Support\Facades\Route;
use Altanay\WebexSms\Http\Controllers\WebhookController;

Route::post('/webhooks/webex-sms', WebhookController::class)
    ->middleware(\Altanay\WebexSms\Http\Middleware\VerifyWebhookSecret::class)
    ->name('webex-sms.webhook');