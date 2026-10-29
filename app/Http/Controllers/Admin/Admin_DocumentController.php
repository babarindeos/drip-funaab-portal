<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Http\Classes\WorkflowClass;
use App\Models\FlowContributor;
use App\Models\Workflow;
use App\Models\GeneralMessage;

class Admin_DocumentController extends Controller
{
    //
    public function index()
    {
        $documents = Document::orderBy('id', 'desc')->paginate(2);
       
        return view('admin.documents.index')->with('documents', $documents);
    }

    public function show(Document $document)
    {
        $current_document_handler = WorkflowClass::getCurrentDocumentHandler($document);
        $current_handler =$current_document_handler;

        $workflow_contributors = FlowContributor::where('doc_id', $document->id)->get();

        $workflow_transactions = Workflow::where('doc_id', $document->id)->get();      
        

        $general_messages = GeneralMessage::where('doc_id', $document->id)->orderBy('created_at', 'desc')->get();

        return view('admin.documents.show', compact('document', 'workflow_contributors', 'current_handler', 'workflow_transactions', 'general_messages'));
        
    }
}
