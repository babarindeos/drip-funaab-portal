<?php

namespace App\Mail\Conference;

use App\Models\Conference\ConferenceApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConferenceDecisionMail extends Mailable
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
            subject: $this->application->status === ConferenceApplication::STATUS_APPROVED ? 'Your conference grant application has been approved' : 'An update on your conference grant application',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.conference.decision',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
