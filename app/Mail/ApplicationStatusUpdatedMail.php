<?php

namespace App\Mail;

use App\Models\ProposalApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProposalApplication $application;
    public bool $shareRemark;

    public function __construct(ProposalApplication $application, bool $shareRemark = false)
    {
        $this->application = $application;
        $this->shareRemark = $shareRemark;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'An update on your proposal application',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.application-status-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
