# Webex SMS for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/yemrealtanay/webex-sms.svg?style=flat-square)](https://packagist.org/packages/yemrealtanay/webex-sms)
[![Total Downloads](https://img.shields.io/packagist/dt/yemrealtanay/webex-sms.svg?style=flat-square)](https://packagist.org/packages/yemrealtanay/webex-sms)

A Laravel package that provides a clean integration with the **Webex Interact SMS API**.  
It includes a lightweight HTTP client, phone number validation, precise segment/cost calculation, and webhook handling with Laravel events.

---

## ✨ Features

- Send SMS through Webex Interact API
- Normalize and validate E.164 phone numbers
- Accurate GSM-7 / Unicode segment & cost calculation
- Merge fields rendering (per-recipient variables)
- Webhook controller for delivery reports and inbound messages
- Laravel events for `Submitted`, `Delivered`, `Failed`, `Clicked`, and `Inbound`
- Idempotency handling for safe webhook processing
- Ready-to-use Facade: `WebexSms::send(...)`

---

## 📦 Installation

```bash
composer require yemrealtanay/webex-sms

Publish the config:

php artisan vendor:publish --tag=webex-sms-config
```

## ⚙️ Configuration

- WEBEX_API_URL=https://api.webexinteract.com
- WEBEX_API_KEY=your-api-key
- WEBEX_API_DEFAULT_SENDER_ID=MySender
- WEBEX_WEBHOOK_SECRET=super-secret-token

## 🚀 Usage

### Sending an SMS

```
use WebexSms;
use Altanay\WebexSms\Support\SmsValidator;

$from   = SmsValidator::normalizeSender(null);
$phones = SmsValidator::normalizePhones(['+905555555555']);

$res = WebexSms::send($from, 'Hello world!', $phones, correlationId: '12345');

dd($res->request_id, $res->messages);
```

### Estimating SMS cost

```
use Altanay\WebexSms\Support\SmsCostCalculator;

$calc = SmsCostCalculator::estimateUnitsForRecipients(
    template: 'Hi ${firstname}, welcome!',
    phones: ['+905555555555'],
    globalMergeFields: ['firstname' => 'Yunus']
);

echo $calc['units']; // number of SMS units reserved
```

## 📡 Webhook Handling

This package ships with a webhook endpoint at:
```
POST /webhooks/webex-sms
```
It automatically:
- Verifies the initial subscription challenge
- Ensures idempotency (no duplicate processing)
- Dispatches Laravel events:
- SmsSubmitted
- SmsDelivered
- SmsFailed
- SmsClicked
- SmsInbound

### Example listener

```
namespace App\Listeners;

use Altanay\WebexSms\Events\SmsDelivered;
use Illuminate\Support\Facades\Log;

class MarkSmsDelivered
{
    public function handle(SmsDelivered $event)
    {
        Log::info('SMS delivered', [
            'tx' => $event->transactionId,
            'cid' => $event->correlationId,
            'msisdn' => $event->phoneNumber,
        ]);
    }
}
```

Register your listeners in EventServiceProvider.

## 🤝 Contributing

Contributions are very welcome!
If you’d like to improve the package, fix a bug, or add a feature:
1.	Fork the repository
2.	Create a feature branch (git checkout -b feature/my-feature)
3.	Commit your changes (git commit -m 'Add new feature')
4.	Push to the branch (git push origin feature/my-feature)
5.	Open a Pull Request

We aim to keep this package community-driven, so ideas and contributions are encouraged.