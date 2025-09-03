<?php

namespace Altanay\WebexSms\Support;

class SmsValidator
{

    public static function normalizeSender(?string $sender): string
    {
        $sender = trim($sender ?: (string)config('webex-sms.sender_id'));
        $len = mb_strlen($sender, 'UTF-8');
        if ($len < 3 || $len > 11) throw new \InvalidArgumentException('Invalid sender length');
        return $sender;
    }

    public static function normalizePhones(array $phones): array
    {
        $out = [];
        foreach ($phones as $raw) {
            $n = preg_replace('/[^\d+]/', '', (string)$raw);
            if (strpos($n, '00') === 0) $n = '+' . substr($n, 2);
            $n = preg_replace('/^\++/', '+', $n);
            if (!preg_match('/^\+\d{8,15}$/', $n)) {
                throw new \InvalidArgumentException("Invalid E.164: $raw");
            }
            $out[] = $n;
        }
        $out = array_values(array_unique($out));
        if (count($out) === 0 || count($out) > 10000) {
            throw new \InvalidArgumentException('Recipient count out of bounds');
        }
        return $out;
    }

}