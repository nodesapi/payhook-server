<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SubscriptionActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $planName;

    /**
     * Create a new message instance.
     */
    public function __construct($tenant, $planName)
    {
        $this->tenant = $tenant;
        $this->planName = $planName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('finance@cekbayar.com', 'Payhook Finance'),
            subject: 'Berhasil! Langganan Payhook Anda Aktif',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription.activated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
