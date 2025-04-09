<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Password Reset OTP',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-otp', // Use 'markdown' if it's a markdown view
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
