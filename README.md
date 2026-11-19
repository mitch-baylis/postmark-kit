# postmark-kit

Shared Postmark mail for the mitch-baylis apps. No vendor SDK — Laravel's native
`postmark` transport does the sending.

## What it gives you

| Piece | Purpose |
|---|---|
| `PostmarkMailer` | Sends a mailable, optionally on a tenant's own server token |
| `SystemMail` | A plain heading + paragraphs + button email |
| `NotificationMailable` | Base class for app-specific mail with the shared layout |
| `SuppressionList` | Hard bounces and spam complaints suppress future sends |
| `SuppressionGuard` | Drops a message to a suppressed recipient before it leaves |
| `PostmarkWebhookController` | Bounce/complaint webhook, always answers 200 |

## Install

```bash
composer require mitchbaylis/postmark-kit
```

The service provider is auto-discovered. Publish nothing unless you need to
change the layout or the config.

## Configure

```dotenv
MAIL_MAILER=postmark
POSTMARK_TOKEN=
POSTMARK_MESSAGE_STREAM_ID=outbound
MAIL_FROM_ADDRESS=notifications@mitchbaylis.com
MAIL_FROM_NAME="Mitch Baylis"
```

`config/mail.php` needs only the default driver:

```php
'default' => env('MAIL_MAILER', 'log'),
```

`config/services.php` needs no Postmark entry — the kit's config owns the token.

## Send

```php
use MitchBaylis\PostmarkKit\Mail\SystemMail;
use MitchBaylis\PostmarkKit\PostmarkMailer;

app(PostmarkMailer::class)->send(
    SystemMail::make(
        subject: 'Daily reminder',
        heading: 'Today: 2 important dates',
        lines: ['Anniversary — Sarah', 'Birthday — Tom'],
        actionUrl: 'https://reminder.mitchbaylis.com',
        actionLabel: 'Open reminders',
    ),
    to: 'mitch@mitchbaylis.com',
);
```

Ordinary `Mail::to(...)->send(...)` also works — the default mailer is Postmark.

## Multi-tenant

Pass the tenant's own server token and stream per send. The kit reconfigures the
mailer for that one call and restores it afterwards, so no tenant ever borrows
another's server.

```php
app(PostmarkMailer::class)->send(
    $mailable,
    to: $recipient,
    token: $tenant->postmark_server_token,
    stream: $tenant->postmark_stream,
);
```

Store the token encrypted (`encrypted` cast) on the tenant row.

## Webhook

`POST /webhooks/postmark` is registered automatically. Disable with
`POSTMARK_WEBHOOK_ENABLED=false`.

Postmark webhooks are unsigned — the URL is the credential. Point the Postmark
server's bounce and complaint webhooks at it:

```
https://<app-host>/webhooks/postmark
```

Hard bounces and spam complaints suppress. Soft/transient bounces do not.

## Migrations

The suppression table ships with the package and is loaded automatically:

```
postmark_suppressions
├── id            uuid
├── email         unique
├── reason        hard_bounce | spam_complaint
├── provider_id   Postmark event ID
├── detail
├── suppressed_at
└── timestamps
```

## Sending domains

Every address in `MAIL_FROM_ADDRESS` must sit on a domain verified in Postmark:
DKIM `pm._domainkey` plus the `pm-bounces` Return-Path CNAME, which must be
**grey-clouded** in Cloudflare — proxying breaks Return-Path and SPF.
