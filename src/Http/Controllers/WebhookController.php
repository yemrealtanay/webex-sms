<?php

namespace Altanay\WebexSms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use Altanay\WebexSms\Events\{SmsSubmitted,SmsDelivered,SmsFailed,SmsClicked,SmsInbound};
class WebhookController
{
    public function __invoke(Request $request)
    {
        $body = $request->json()->all();

        if (isset($body['verification_secret'])) {
            return response($body['verification_secret'], 200);
        }

        $data   = $body['data'] ?? [];
        $status = $data['status'] ?? null;
        $txId   = $data['transaction_id'] ?? null;

        if (!$status || !$txId) {
            return response()->noContent(Response::HTTP_ACCEPTED);
        }

        $key = 'webex_tx_'.$txId;
        if (!Cache::add($key, 1, now()->addMinutes((int)config('webex-sms.webhook_idem_ttl', 30)))) {
            return response()->noContent();
        }

        $event = match ($status) {
            'submitted' => SmsSubmitted::class,
            'delivered' => SmsDelivered::class,
            'failed'    => SmsFailed::class,
            'clicked'   => SmsClicked::class,
            'inbound'   => SmsInbound::class,
            default     => null
        };
        if ($event) Event::dispatch(new $event($body));

        return response()->noContent();
    }

}