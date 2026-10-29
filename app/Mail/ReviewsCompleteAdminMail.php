<?php

namespace App\Mail;

use App\Models\ProposalApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewsCompleteAdminMail extends Mailable
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
            subject: 'All reviewers have scored a proposal',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviews-complete-admin',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
