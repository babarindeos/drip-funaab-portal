<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowContributor;
use App\Models\Document;
use App\Models\GeneralMessage;

class Staff_GeneralMessageController extends Controller
{
    public function __construct()
    {
        //secho "am here";
    }

    //
    public function index(Document $document)
    {
        
        $workflow_contributors = FlowContributor::where('doc_id', $document->id)->get();

        $messages = GeneralMessage::where('doc_id', $document->id)->orderBy('created_at', 'desc')->get();
        
        return view('staff.general_messages.index', compact('document','workflow_contributors', 'messages'));
    }

    public function store(Request $request, Document $document)
    {

        $formFields = $request->validate([
            'message' => 'required'
        ]);

        $formFields['doc_id'] = $document->id;
        $formFields['sender_id'] = auth()->user()->id;

        try
        {
            $create = GeneralMessage::create($formFields);


        }
        catch(\Exception $e)
        {

        }

        return redirect()->back();


    }
}
