<?php

namespace Altanay\WebexSms\Services;

class Client
{
    private Guzzle $http;
    private string $key;

    public function __construct()
    {
        $this->http = new Guzzle([
            'base_uri'    => rtrim(config('webex-sms.base_uri'), '/') . '/',
            'timeout'     => 15,
            'http_errors' => false,
        ]);
        $this->key = (string) config('webex-sms.api_key');
    }

    public function send(string $from, string $message, array $phones, ?string $correlationId = null): object
    {
        $payload = [
            'from'         => $from,
            'message_body' => $message,
            'to'           => [[
                    'phone' => $phones,
                ]] + ($correlationId ? ['correlation_id' => $correlationId] : []),
        ];

        $resp = $this->http->post('v1/sms', [
            'headers' => [
                'X-AUTH-KEY'   => $this->key,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $status = $resp->getStatusCode();
        $body   = (string) $resp->getBody();
        $json   = json_decode($body);

        if ($status < 200 || $status >= 300) {
            $err = isset($json->errors) ? json_encode($json->errors) : $body;
            throw new \RuntimeException("Webex error: HTTP $status $err", $status);
        }
        if (!isset($json->messages)) {
            throw new \RuntimeException('Unexpected Webex response (no messages)');
        }
        return $json;
    }

}