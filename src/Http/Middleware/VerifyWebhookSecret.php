<?php

namespace Altanay\WebexSms\Http\Middleware;

class VerifyWebhookSecret
{
    public function handle(Request $request, Closure $next)
    {
        $secret = config('webex-sms.webhook_secret');
        if (!$secret) return $next($request);

        if ($request->header('X-Webex-Secret') !== $secret) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}