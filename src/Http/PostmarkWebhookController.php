<?php

namespace MitchBaylis\PostmarkKit\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MitchBaylis\PostmarkKit\Suppression\SuppressionList;

/**
 * Postmark's bounce and complaint webhook.
 *
 * Postmark signs nothing, so the URL is the credential. Every event is answered
 * 200 immediately: a non-2xx teaches Postmark to retry and eventually disable
 * the hook, which loses bounces silently.
 */
final class PostmarkWebhookController
{
    public function __construct(private readonly SuppressionList $list) {}

    public function __invoke(Request $request): JsonResponse
    {
        $type = (string) ($request->input('RecordType') ?? $request->input('Type') ?? '');
        $email = (string) ($request->input('Email') ?? '');
        $id = $request->input('ID');

        match (strtolower($type)) {
            'bounce' => $this->handleBounce($request, $email, $id),
            'spamcomplaint' => $this->list->suppress(
                $email,
                SuppressionList::REASON_SPAM_COMPLAINT,
                is_string($id) ? $id : null,
                'spam complaint',
            ),
            default => null,
        };

        return response()->json(['ok' => true]);
    }

    private function handleBounce(Request $request, string $email, mixed $id): void
    {
        $bounceType = strtolower((string) $request->input('Type', ''));

        // Only a permanent failure is a dead address. Soft/transient bounces
        // (mailbox full, greylisting) are retried by Postmark and are not
        // grounds for suppression.
        if (! in_array($bounceType, ['hardbounce', 'bademailaddress'], true)) {
            return;
        }

        if ($email === '') {
            Log::warning('postmark: hard bounce without an email address', $request->all());

            return;
        }

        $this->list->suppress(
            $email,
            SuppressionList::REASON_HARD_BOUNCE,
            is_string($id) ? $id : null,
            (string) ($request->input('Details') ?? $request->input('Description') ?? ''),
        );
    }
}
