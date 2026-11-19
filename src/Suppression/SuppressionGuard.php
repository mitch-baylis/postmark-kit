<?php

namespace MitchBaylis\PostmarkKit\Suppression;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;

/**
 * Drops a message addressed to any suppressed recipient.
 *
 * Returning false from a `MessageSending` listener cancels the send, so this
 * sits between the app and every mailer without touching call sites.
 */
final class SuppressionGuard
{
    public function __construct(private readonly SuppressionList $list) {}

    public function handle(MessageSending $event): bool
    {
        $blocked = [];

        foreach ($event->message->getTo() as $address) {
            if ($this->list->isSuppressed($address->getAddress())) {
                $blocked[] = $address->getAddress();
            }
        }

        if ($blocked === []) {
            return true;
        }

        Log::warning('postmark: suppressed recipient, message dropped', [
            'recipients' => $blocked,
            'subject' => $event->message->getSubject(),
        ]);

        return false;
    }
}
