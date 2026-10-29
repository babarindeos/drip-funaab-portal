<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProposalApplication;

use App\Mail\ReviewerInvitationMail;
use App\Models\Reviewer;
use App\Models\ProposalReviewers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Admin_ProposalReviewerController extends Controller
{
    //
    public function send_to_reviewer(ProposalApplication $proposal_application)
    {
        $reviewers = Reviewer::orderBy('created_at', 'asc')->get();

        return view('admin.proposal_reviewers.send_to_reviewer', compact('proposal_application', 'reviewers'));
    }

    public function post_send_to_reviewer(Request $request, ProposalApplication $proposal_application)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:reviewers,id',
        ], [
            'reviewer_id.required' => 'Please search for a reviewer and pick one from the suggestions list.',
            'reviewer_id.exists' => 'That reviewer could not be found. Please pick one from the suggestions list.',
        ]);

        $reviewer = Reviewer::findOrFail($request->reviewer_id);

        // never send the same application to the same reviewer twice
        $already_sent = ProposalReviewers::where('proposal_application_id', $proposal_application->id)
                                          ->where('reviewer_id', $reviewer->id)
                                          ->exists();

        if ($already_sent)
        {
            return redirect()->back()->with([
                'error' => true,
                'status' => 'fail',
                'message' => 'This proposal has already been sent to '.$reviewer->name.'. Use "Resend" in the list below if they need the link again.'
            ]);
        }

        try
        {
            $proposal_reviewer = new ProposalReviewers();
            $proposal_reviewer->uuid = Str::orderedUuid();
            $proposal_reviewer->proposal_application_id = $proposal_application->id;
            $proposal_reviewer->proposal_application_uuid = $proposal_application->uuid;
            $proposal_reviewer->reviewer_id = $reviewer->id;
            $proposal_reviewer->reviewer_uuid = $reviewer->uuid;
            $proposal_reviewer->save();
        }
        catch(\Exception $e)
        {
            report($e);

            return redirect()->back()->with([
                'error' => true,
                'status' => 'fail',
                'message' => 'An error occurred sending the proposal to the Reviewer'
            ]);
        }

        return redirect()->back()->with($this->deliver_invitation($proposal_reviewer, 'sent'));
    }

    /**
     * Rebuild the link (using the current DRIP_BASE_URL) and email it again.
     * Useful after the base URL changes, or if the first email failed.
     */
    public function resend(ProposalReviewers $proposal_reviewer)
    {
        if ($proposal_reviewer->is_reviewed)
        {
            return redirect()->back()->with([
                'error' => true,
                'status' => 'fail',
                'message' => 'This reviewer has already submitted their review, so the invitation cannot be resent.'
            ]);
        }

        return redirect()->back()->with($this->deliver_invitation($proposal_reviewer, 'resent'));
    }

    /**
     * Builds and saves the invitation (link + message), then tries to email it.
     * The record is saved either way, so if the email fails the admin can still
     * copy the link from the list and send it by hand.
     */
    private function deliver_invitation(ProposalReviewers $proposal_reviewer, string $verb): array
    {
        $proposal_reviewer->load('reviewer', 'proposal_application.call_for_proposal');

        $proposal_reviewer->review_link = $proposal_reviewer->buildReviewLink();
        $proposal_reviewer->message = $proposal_reviewer->composeInvitationMessage();
        $proposal_reviewer->emailed_at = null;
        $proposal_reviewer->email_error = null;

        $name = $proposal_reviewer->reviewer->name;
        $email = $proposal_reviewer->reviewer->email;

        try
        {
            Mail::to($email)->send(new ReviewerInvitationMail($proposal_reviewer));
            $proposal_reviewer->emailed_at = now();
            $result = [
                'error' => true,
                'status' => 'success',
                'message' => 'The proposal has been '.$verb.' to '.$name.'. The review link was emailed to '.$email.' and is saved in the list below.'
            ];
        }
        catch(\Throwable $e)
        {
            report($e);
            $proposal_reviewer->email_error = Str::limit($e->getMessage(), 500);
            $result = [
                'error' => true,
                'status' => 'fail',
                'message' => 'The proposal has been '.$verb.' to '.$name.', but the email could not be delivered. Copy the review link from the list below and send it to them directly.'
            ];
        }

        $proposal_reviewer->save();

        return $result;
    }

    public function destroy(ProposalReviewers $proposal_reviewer)
    {
        // Removing an assignment also deletes that reviewer's scoring sheet
        // (database cascade), so never allow it once they have reviewed.
        if ($proposal_reviewer->is_reviewed)
        {
            return redirect()->back()->with([
                'error' => true,
                'status' => 'fail',
                'message' => 'This reviewer has already submitted their review and can no longer be removed.'
            ]);
        }

        try
        {
            $proposal_reviewer->delete();

            $data = [
                'error' => true,
                'status' => 'success',
                'message' => 'The proposal reviewer has been removed'
            ];

        }
        catch(\Exception $e)
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'An error occurred removing the proposal reviewer'
            ];
        }

        return redirect()->back()->with($data);
    }
}
