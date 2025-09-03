<?php

namespace Altanay\WebexSms\Support;

class SmsCostCalculator
{
    private static $GSM7 = <<<'GSM7'
@£$¥èéùìòÇ
Øø
Åå_ΦΓΛΩΠΨΣΘΞÆæßÉ !"#¤%&'()*+,-./0123456789:;<=>?ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà^{}\ [~]|€
GSM7;

    private static $GSM7_EXT = <<<'EXT'
^€{}[]~\|
EXT;

    public static function isGsm7(string $text): bool
    {
        foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
            if (strpos(self::$GSM7, $ch) === false) {
                return false;
            }
        }
        return true;
    }

    public static function gsm7SeptetLength(string $text): int
    {
        $len = 0;
        foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
            $len += (strpos(self::$GSM7_EXT, $ch) !== false) ? 2 : 1;
        }
        return $len;
    }

    public static function ucs2Length(string $text): int
    {
        $utf16 = mb_convert_encoding($text, 'UTF-16BE', 'UTF-8');
        return (int) (strlen($utf16) / 2);
    }

    public static function segmentCount(string $text): int
    {
        if (self::isGsm7($text)) {
            $septets = self::gsm7SeptetLength($text);
            if ($septets <= 160) return 1;
            return (int) ceil(($septets - 160) / 153) + 1;
        }

        $ucs2 = self::ucs2Length($text);
        if ($ucs2 <= 70) return 1;
        return (int) ceil(($ucs2 - 70) / 63) + 1;
    }

    public static function renderMessage(string $template, array $global = [], array $recipient = []): string
    {
        $vars = array_merge($global, $recipient);
        if (!$vars) return $template;

        return preg_replace_callback('/\$\{([a-zA-Z0-9_]+)\}/', function ($m) use ($vars) {
            $k = $m[1];
            return array_key_exists($k, $vars) ? (string) $vars[$k] : $m[0];
        }, $template);
    }

    /**
     * @param string $template
     * @param string[] $phones
     * @param array $globalMergeFields
     * @param array $perRecipientMerge
     * @param int $safetySegmentsPerMsg
     * @return array
     */
    public static function estimateUnitsForRecipients(
        string $template,
        array $phones,
        array $globalMergeFields = [],
        array $perRecipientMerge = [],
        int $safetySegmentsPerMsg = 0
    ): array {
        $total = 0;
        $map = [];

        foreach ($phones as $msisdn) {
            $rvars = $perRecipientMerge[$msisdn] ?? [];
            $body  = self::renderMessage($template, $globalMergeFields, $rvars);
            $seg   = self::segmentCount($body);
            $seg  += max(0, $safetySegmentsPerMsg);
            $map[$msisdn] = $seg;
            $total += $seg;
        }

        return ['units' => $total, 'perRecipient' => $map];
    }

}