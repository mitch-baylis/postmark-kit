<?php

namespace MitchBaylis\PostmarkKit\Mail;

/**
 * A plain system email: a heading, some paragraphs and an optional button.
 *
 *     Postmark::send(SystemMail::make(
 *         subject: 'Daily reminder',
 *         heading: 'Today: 2 important dates',
 *         lines: ['Anniversary — Sarah', 'Birthday — Tom'],
 *         actionUrl: 'https://reminder.mitchbaylis.com',
 *         actionLabel: 'Open reminders',
 *     ), to: 'mitch@mitchbaylis.com');
 */
final class SystemMail extends NotificationMailable
{
    /**
     * @param  array<int, string>  $lines
     */
    public function __construct(
        private readonly string $subjectLine,
        private readonly string $mailHeading,
        private readonly array $lines = [],
        private readonly ?string $actionUrl = null,
        private readonly ?string $actionLabel = null,
        private readonly ?string $footer = null,
    ) {}

    /**
     * @param  array<int, string>  $lines
     */
    public static function make(
        string $subject,
        string $heading,
        array $lines = [],
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?string $footer = null,
    ): self {
        return new self($subject, $heading, $lines, $actionUrl, $actionLabel, $footer);
    }

    protected function mailSubject(): string
    {
        return $this->subjectLine;
    }

    /**
     * @return array<string, mixed>
     */
    protected function mailData(): array
    {
        return [
            'heading' => $this->mailHeading,
            'lines' => $this->lines,
            'actionUrl' => $this->actionUrl,
            'actionLabel' => $this->actionLabel,
            'footer' => $this->footer,
        ];
    }
}
