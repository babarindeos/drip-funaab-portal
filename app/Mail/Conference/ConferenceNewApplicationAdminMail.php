<?php

namespace App\Mail\Conference;

use App\Models\Conference\ConferenceApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConferenceNewApplicationAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public ConferenceApplication $application;

    public function __construct(ConferenceApplication $application)
    {
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New conference grant application received',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.conference.new-application-admin',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
