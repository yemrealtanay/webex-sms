<?php

return [
    'base_uri'  => env('WEBEX_API_URL', 'https://api.webexinteract.com'),
    'api_key'   => env('WEBEX_API_KEY', ''),
    'sender_id' => env('WEBEX_API_DEFAULT_SENDER_ID', ''),
    'webhook_secret' => env('WEBEX_WEBHOOK_SECRET', null),
    'webhook_idem_ttl' => env('WEBEX_WEBHOOK_IDEM_TTL', 30),
];