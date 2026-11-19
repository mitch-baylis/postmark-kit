<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server token
    |--------------------------------------------------------------------------
    |
    | The default Postmark server token. Multi-tenant apps pass a per-tenant
    | token to the mailer instead and leave this as the platform fallback.
    |
    */

    'token' => env('POSTMARK_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Message stream
    |--------------------------------------------------------------------------
    |
    | Postmark streams separate transactional mail from broadcast mail so a
    | complaint on one never suppresses the other. `outbound` is the default
    | transactional stream on every Postmark server.
    |
    */

    'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID', 'outbound'),

    /*
    |--------------------------------------------------------------------------
    | Default sender
    |--------------------------------------------------------------------------
    |
    | Every system mail takes its From from here unless a mailable overrides
    | it. The address must be on a verified sending domain.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'notifications@mitchbaylis.com'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Mitch Baylis')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Suppression guard
    |--------------------------------------------------------------------------
    |
    | When enabled, any message addressed to a suppressed recipient is dropped
    | before it reaches Postmark. Postmark also suppresses server-side; this is
    | the second net, and it keeps the app's own logs honest.
    |
    */

    'suppress_recipients' => env('POSTMARK_SUPPRESS_RECIPIENTS', true),

    /*
    |--------------------------------------------------------------------------
    | Webhook
    |--------------------------------------------------------------------------
    |
    | Postmark webhooks are unsigned, so the URL is the credential. Keep the
    | path unguessable rather than relying on a signature that does not exist.
    |
    */

    'webhook' => [
        'enabled' => env('POSTMARK_WEBHOOK_ENABLED', true),
        'path' => env('POSTMARK_WEBHOOK_PATH', 'webhooks/postmark'),
        'middleware' => ['api'],
    ],

];
