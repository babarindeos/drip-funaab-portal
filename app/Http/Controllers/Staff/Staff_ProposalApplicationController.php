<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CallForProposal;
use \Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\ProposalApplication;
use App\Http\Classes\Notifier;
use App\Mail\ApplicationSubmittedMail;
use App\Mail\NewApplicationAdminMail; 

class Staff_ProposalApplicationController extends Controller
{
    //
    /**
     * Full listing of calls for proposals for a logged-in staff member —
     * previously there was no page for this; staff could only reach a call
     * via a direct link from the homepage teaser.
     */
    public function index()
    {
        $call_for_proposals = CallForProposal::where(function ($q) {
                                                $q->whereNull('status')
                                                  ->orWhereNotIn('status', [CallForProposal::STATUS_DRAFT]);
                                              })
                                              ->orderBy('close_date', 'desc')
                                              ->paginate(10);

        // the person's own application for each call on this page
        $mine = ProposalApplication::where('user_id', Auth::id())
                                    ->whereIn('call_for_proposal_id', $call_for_proposals->pluck('id'))
                                    ->get()
                                    ->keyBy('call_for_proposal_id');

        return view('staff.proposals.index', compact('call_for_proposals', 'mine'));
    }

    public function application($uuid)
    {
        $call_for_proposal = CallForProposal::where('uuid', $uuid)->firstOrFail();

        $my_application = ProposalApplication::where('user_id', Auth::id())
                                                ->where('call_for_proposal_id', $call_for_proposal->id)
                                                ->first();

        $has_submitted_application = (bool) $my_application;

        return view('staff.proposals.application', compact('call_for_proposal', 'has_submitted_application', 'my_application'));
    }

    public function store_application(Request $request, $uuid)
    {
        $call_for_proposal = CallForProposal::where('uuid', $uuid)->firstOrFail();

        // Block submissions to a call that is no longer open, even if someone
        // still has the application link/page open in their browser.
        if (!$call_for_proposal->isOpen())
        {
            return redirect()->route('staff.call_for_proposals.application', ['uuid' => $uuid])
                              ->with(['error' => true, 'status' => 'fail', 'message' => 'This call is no longer open for applications.']);
        }

        $has_submitted_application = ProposalApplication::where('user_id', Auth::id())
                                                ->where('call_for_proposal_id', $call_for_proposal->id)
                                                ->exists();

        if ($has_submitted_application)
        {
            return redirect()->route('staff.call_for_proposals.application', ['uuid' => $uuid])
                              ->with(['error' => true, 'status' => 'fail', 'message' => 'You have already submitted an application for this call.']);
        }

        // validate the application form fields — kept outside the try/catch below
        // so a validation failure redirects back with the usual field errors
        // instead of being swallowed as a generic exception.
        $request->validate([
            'principal_investigator' => 'required|string|max:255',
            'proposal_title' => 'required|string|max:255',
            'proposal_title_file' => 'required|file|mimes:doc,docx,pdf,odt|max:20480', // max file size of 20MB
            'proposal_file' => 'required|file|mimes:doc,docx,pdf,odt|max:20480', // max file size of 20MB
            'college_review' => 'required|file|mimes:doc,docx,pdf,odt|max:20480', // max file size of 20MB
            'proposal_description' => 'required|string|max:5000'
        ]);

        try
        {
                // handle the uploaded proposal title file
                if ($request->hasFile('proposal_title_file'))
                {
                    $proposalTitleFile = $request->file('proposal_title_file');

                    $new_proposal_title_filename = $uuid."_".auth()->user()->id."_title_".time().".".$proposalTitleFile->getClientOriginalExtension();

                    $proposalTitleFile->storeAs('proposals_title', $new_proposal_title_filename, config('drip.documents_disk'));

                
                }

                // handle the uploaded proposal file
                if ($request->hasFile('proposal_file'))
                {
                    $proposalFile = $request->file('proposal_file');

                    $new_proposal_filename = $uuid."_".auth()->user()->id."_".time().".".$proposalFile->getClientOriginalExtension();

                    $proposalFile->storeAs('proposals', $new_proposal_filename, config('drip.documents_disk'));

                
                }


                 // handle the uploaded college review file
                if ($request->hasFile('college_review'))
                {
                    $collegeReviewFile = $request->file('college_review');

                    $new_college_review_filename = $uuid."_".auth()->user()->id."_college_review_".time().".".$collegeReviewFile->getClientOriginalExtension();

                    $collegeReviewFile->storeAs('college_reviews', $new_college_review_filename, config('drip.documents_disk'));

                
                }
               



                // save the proposal application details to the database
                    // you can create a ProposalApplication model and save the details there
                    // for example:
                    
                    $application = ProposalApplication::create([
                        'uuid' => Str::orderedUuid(),
                        'user_id' => Auth::id(),
                        'call_for_proposal_id' => $call_for_proposal->id,
                        'proposal_title_file' => "proposals_title/".$new_proposal_title_filename,
                        'proposal_file' => "proposals/".$new_proposal_filename,
                        'principal_investigator' => $request->principal_investigator,
                        'proposal_title' => $request->proposal_title,  
                        'college_review' => "college_reviews/".$new_college_review_filename,
                        'proposal_description' => $request->proposal_description
                    ]);
        }
        catch (\Exception $e)
        {
            report($e); // logs the full exception for admins/devs to review

            return redirect()->route('staff.call_for_proposals.application', ['uuid' => $uuid])
                              ->withInput()
                              ->with(['error' => true, 'status' => 'fail', 'message' => 'Something went wrong while submitting your application. Please try again, and contact DRIP support if the problem continues.']);
        }


        // The application is saved at this point. The emails below are a
        // courtesy: Notifier::send() never throws, so a mail problem can't
        // undo or block the submission.
        $application->load('owner', 'call_for_proposal');

        Notifier::send(Auth::user()->email, new ApplicationSubmittedMail($application), 'application submitted - applicant');

        $admin_recipients = Notifier::admin_recipients();
        if (!empty($admin_recipients)) {
            Notifier::send($admin_recipients, new NewApplicationAdminMail($application), 'application submitted - admin alert');
        }

        return redirect()->route('staff.call_for_proposals.application', ['uuid' => $uuid])->with('success', 'Your proposal application has been submitted successfully.');
    }
}
