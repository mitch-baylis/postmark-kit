<?php

namespace MitchBaylis\PostmarkKit\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The base for every system/notification email sent through the kit.
 *
 * The envelope takes its From from `postmark.from` unless a child overrides
 * `sender()`, and the content renders the shared layout.
 */
abstract class NotificationMailable extends Mailable
{
    use Queueable, SerializesModels;

    abstract protected function mailSubject(): string;

    /**
     * The default sender, from `config('postmark.from')`.
     *
     * @return array{0: Address, 1: Address}|Address|null
     */
    protected function sender(): Address|array|null
    {
        $address = (string) config('postmark.from.address');
        $name = (string) config('postmark.from.name');

        if ($address === '') {
            return null;
        }

        return new Address($address, $name);
    }

    protected function view(): string
    {
        return 'postmark-kit::mail.notification';
    }

    /**
     * @return array<string, mixed>
     */
    protected function mailData(): array
    {
        return [];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->sender(),
            subject: $this->mailSubject(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->view(),
            with: array_merge([
                'appName' => (string) config('app.name'),
            ], $this->mailData()),
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}
