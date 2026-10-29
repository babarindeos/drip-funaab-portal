<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The "set a new password" email, in the same style as the portal's other emails. */
class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $url, public int $minutes)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Set a new password for your DRIP account',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.password-reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
