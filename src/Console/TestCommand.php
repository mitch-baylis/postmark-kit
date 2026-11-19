<?php

namespace MitchBaylis\PostmarkKit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use MitchBaylis\PostmarkKit\Mail\SystemMail;
use Throwable;

/**
 * Sends one real email through the configured Postmark transport.
 *
 *     php artisan postmark:test
 *     php artisan postmark:test someone@example.com
 */
final class TestCommand extends Command
{
    protected $signature = 'postmark:test
                            {to? : Recipient address (defaults to the configured test recipient)}';

    protected $description = 'Send a test email through Postmark to prove the mail path works';

    public function handle(): int
    {
        $to = $this->argument('to') ?: config('postmark.test_recipient');

        $app = config('app.name', 'Application');

        $this->components->info("Sending test email to {$to} via ".config('mail.default'));

        try {
            Mail::to($to)->send(SystemMail::make(
                subject: "{$app}: Postmark test",
                heading: 'Postmark is wired up',
                lines: [
                    "This message was sent by {$app}.",
                    'From: '.config('postmark.from.address'),
                    'Stream: '.config('postmark.message_stream_id'),
                    'Sent: '.now()->toDateTimeString(),
                ],
                footer: 'If this landed in your inbox, the mail path is working.',
            ));
        } catch (Throwable $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $this->components->info("Accepted by Postmark for {$to}.");

        return self::SUCCESS;
    }
}
