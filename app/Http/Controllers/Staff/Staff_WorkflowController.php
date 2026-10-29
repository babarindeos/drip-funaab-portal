<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Staff;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use App\Models\FlowContributor;
use App\Models\Workflow;
use App\Http\Classes\WorkflowClass;
use App\Models\PrivateMessage;
use App\Models\GeneralMessage;
use App\Http\Classes\OrganClass;

class Staff_WorkflowController extends Controller
{
    //
    public function flow(Document $document)
    {   
        $current_document_handler = WorkflowClass::getCurrentDocumentHandler($document);
        $current_handler =$current_document_handler;

        $workflow_contributors = FlowContributor::where('doc_id', $document->id)->get();

        $workflow_transactions = Workflow::where('doc_id', $document->id)->get();

        // get private message for the authenticated user
        $my_private_messages = PrivateMessage::where('doc_id', $document->id)
                                              ->where('recipient_id', auth()->user()->id)
                                              ->where('read', false)->get();
        

        $general_messages = GeneralMessage::where('doc_id', $document->id)->orderBy('created_at', 'desc')->get();

        return view('staff.workflows.flow', compact('document', 'workflow_contributors', 'current_handler', 'workflow_transactions', 'my_private_messages', 'general_messages'));
    }

    public function add_contributor(Document $document)
    {
        $staff = null;
        $workflow_contributors = FlowContributor::where('doc_id', $document->id)->get();
        
        return view('staff.workflows.add_contributor', compact('document', 'staff', 'workflow_contributors'));
    }

    public function post_add_contributor(Request $request, Document $document)
    {
        $formFields = $request->validate([
            'user_id' => 'required'
        ]);

        $data = [
            'doc_id' => $document->id,
            'user_id' => $formFields['user_id'],
            'added_by' => auth()->user()->id
        ];

    
        $contributor_already_added = FlowContributor::where('doc_id', $document->id)
                                                    ->where('user_id', $formFields['user_id'])->exists();
        
        if ($contributor_already_added)
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => "The Contributor has aready been added to this Workflow"
            ];

            return redirect()->back()->with($data);
        }

               
        

        try
        {
            $createContributor = FlowContributor::create($data);

            if ($createContributor)
            {
                return redirect()->route('staff.workflows.flow', ['document'=>$document]);
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred adding the Contributor'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred adding the Contributor: '.$e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
        
    }

    public function search_staff(Request $request, Document $document)
    {
         $formFields = $request->validate([
            'staffno' => ['required', 'string']
         ]);

         

         $staff = Staff::where('fileno', $request->input('staffno'))->first();

         //dd(auth()->user()->id);
         //dd(Auth::user()->id);
         if ($staff == null || $staff=="")
         {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => "There's no Staff with that File No"
            ];

            $staff = null;
            return redirect()->back()->with($data);
         }
         else if ($staff->user_id == auth()->user()->id)
         {
             $data = [
                'error' => true,
                'status' => 'fail',
                'message' => "You can't add yourself as a contributor"
             ];

             $staff = null;
             return redirect()->back()->with($data);
         }         
         
         $organ = OrganClass::getOrganBySegment($staff);

        // dd($organ->name);

         return redirect()->back()->with(['error'=>false, 'status'=>'success', 'staff' => $staff, 'organ'=>$organ]);
         //return view('staff.workflows.add_contributor', compact('document','staff'))->with(['error'=>false, 'status'=>'success']);
         
    }

    public function forward_document(Request $request, Document $document)
    {
        
        $formFields = $request->validate([
            'contributor' => ['required'],
            'status' => ['required'],
            'comment' => ['required']
        ]);

        
        

        try
        {
           
            $workflow_data = [
                'doc_id' => $document->id,
                'sender_id' => auth()->user()->id,
                'recipient_id' => $formFields['contributor'],
                'status' => $formFields['status'],
                'comment' => $formFields['comment']
            ];            

            $forward = Workflow::create($workflow_data);

            return redirect()->back();

        }
        catch(\Exception $e)
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'An error occurred forwarding the document: '.$e->getMessage()
            ];

            return redirect()->back()->with($data);
        }

        
    }


    public function notification_update(Workflow $workflow)
    {
        if ($workflow->read == 0)
        {
            $read_status_update = WorkflowClass::updateReadStatus($workflow);
        }

        // // get current document handler
        // $current_document_handler = getCurrentDocumentHandler($document);
        // $current_handler = $current_document_handler;

        // // get workflow contributors
        // $workflow_contributors = FlowContributor::where('doc_id', $document->id)->get();

        return redirect()->route('staff.workflows.flow',["document"=>$workflow->doc_id]);
    }

    
}
