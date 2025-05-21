<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $reason;

    public function __construct($tenant, $reason = null)
    {
        $this->tenant = $tenant;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Bakery Registration Has Been Rejected',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-rejected',
            with: [
                'tenant' => $this->tenant,
                'reason' => $this->reason,
            ],
        );
    }
} 