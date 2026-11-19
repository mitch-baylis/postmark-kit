<?php

namespace MitchBaylis\PostmarkKit;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a mailable through Postmark, optionally on a tenant's own server.
 *
 * Laravel's native `postmark` transport already reads its token from
 * `mail.mailers.postmark.token`, so tenancy is a matter of reconfiguring that
 * array for the duration of one send, purging the resolved mailer, and putting
 * the config back afterwards. No vendor SDK, no custom transport.
 */
final class PostmarkMailer
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $original = null;

    public function send(
        Mailable $mailable,
        string|array|null $to = null,
        ?string $token = null,
        ?string $stream = null,
    ): void {
        $this->configure($token, $stream);

        try {
            $pending = Mail::mailer('postmark');

            if ($to !== null) {
                $pending = $pending->to($to);
            }

            $pending->send($mailable);
        } finally {
            $this->restore();
        }
    }

    /**
     * Queue instead of send, with the same per-tenant override.
     */
    public function queue(
        Mailable $mailable,
        string|array|null $to = null,
        ?string $token = null,
        ?string $stream = null,
    ): void {
        $this->configure($token, $stream);

        try {
            $pending = Mail::mailer('postmark');

            if ($to !== null) {
                $pending = $pending->to($to);
            }

            $pending->queue($mailable);
        } finally {
            $this->restore();
        }
    }

    private function configure(?string $token, ?string $stream): void
    {
        $this->original ??= config('mail.mailers.postmark', ['transport' => 'postmark']);

        $config = $this->original;

        if ($token !== null && $token !== '') {
            $config['token'] = $token;
        }

        if ($stream !== null && $stream !== '') {
            $config['message_stream_id'] = $stream;
        }

        config(['mail.mailers.postmark' => $config]);

        $this->purge();
    }

    private function restore(): void
    {
        if ($this->original === null) {
            return;
        }

        config(['mail.mailers.postmark' => $this->original]);
        $this->original = null;

        $this->purge();
    }

    /**
     * Drop the resolved mailer so the next send rebuilds it from config.
     *
     * Guarded because `Mail::fake()` swaps in a fake with no purge surface.
     */
    private function purge(): void
    {
        $manager = Mail::getFacadeRoot();

        if (method_exists($manager, 'purge')) {
            $manager->purge('postmark');
        }
    }
}
