<?php

namespace App\Http\Classes;

use App\Http\Interfaces\WorkflowInterface;
use App\Models\Workflow;
use App\Models\Document;

class WorkflowClass implements WorkflowInterface
{
    public static function updateReadStatus(Workflow $workflow)
    {
        $workflow['read'] = true;
        $workflow->update();
    }

    public static function getCurrentDocumentHandler(Document $document)
    {
        // current handler 
        $last_workflow = Workflow::where('doc_id', $document->id)->orderBy('id', 'desc')->first();

        // get current handler
        if ($last_workflow==null)
        {
            $current_handler = $document->uploader;
        }
        else
        {
            $current_handler = $last_workflow->recipient_id;
        }

        return $current_handler;
    }
}