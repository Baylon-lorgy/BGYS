<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $password;

    public function __construct($tenant, $password)
    {
        $this->tenant = $tenant;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Bakery Registration Has Been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-approved',
            with: [
                'tenant' => $this->tenant,
                'password' => $this->password,
            ],
        );
    }
} 