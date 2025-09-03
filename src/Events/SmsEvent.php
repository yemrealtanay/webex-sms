<?php

namespace Altanay\WebexSms\Events;

class SmsEvent
{
    public array $payload;
    public string $transactionId;
    public ?string $correlationId;
    public ?string $phoneNumber;
    public string $status;

    public function __construct(array $payload)
    {
        $this->payload       = $payload;
        $data                = $payload['data'] ?? [];

        $this->transactionId = $data['transaction_id'] ?? '';
        $this->correlationId = $payload['correlation_id'] ?? null;
        $this->phoneNumber   = $data['phone_number'] ?? null;
        $this->status        = $data['status'] ?? '';
    }

}