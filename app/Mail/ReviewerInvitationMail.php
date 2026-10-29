<?php

namespace App\Mail;

use App\Models\ProposalReviewers;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProposalReviewers $proposalReviewer;

    public function __construct(ProposalReviewers $proposalReviewer)
    {
        $this->proposalReviewer = $proposalReviewer;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation to review a research proposal',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviewer-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
