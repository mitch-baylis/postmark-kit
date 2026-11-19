<?php

namespace MitchBaylis\PostmarkKit\Suppression;

/**
 * The app's own record of addresses Postmark rejected.
 *
 * Hard bounces and spam complaints suppress. Soft/transient bounces do not:
 * a full mailbox is not a dead address, and suppressing it would lose a
 * customer who was simply away.
 */
final class SuppressionList
{
    public const REASON_HARD_BOUNCE = 'hard_bounce';

    public const REASON_SPAM_COMPLAINT = 'spam_complaint';

    public function isSuppressed(string $email): bool
    {
        return SuppressedAddress::query()
            ->where('email', $this->normalise($email))
            ->exists();
    }

    public function suppress(
        string $email,
        string $reason,
        ?string $providerId = null,
        ?string $detail = null,
    ): SuppressedAddress {
        return SuppressedAddress::query()->updateOrCreate(
            ['email' => $this->normalise($email)],
            [
                'reason' => $reason,
                'provider_id' => $providerId,
                'detail' => $detail,
                'suppressed_at' => now(),
            ],
        );
    }

    public function release(string $email): void
    {
        SuppressedAddress::query()
            ->where('email', $this->normalise($email))
            ->delete();
    }

    private function normalise(string $email): string
    {
        return strtolower(trim($email));
    }
}
