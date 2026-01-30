<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfitAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $lastWeekProfit;
    public $prevWeekProfit;
    public $dropPercent;
    public $topDrops;

    /**
     * Create a new message instance.
     */
    public function __construct($lastWeekProfit, $prevWeekProfit, $dropPercent, $topDrops)
    {
        $this->lastWeekProfit = $lastWeekProfit;
        $this->prevWeekProfit = $prevWeekProfit;
        $this->dropPercent = $dropPercent;
        $this->topDrops = $topDrops;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Alert: Significant Profit Drop Detected',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.profit-alert',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
