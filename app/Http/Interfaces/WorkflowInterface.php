<?php

namespace App\Http\Interfaces;

use App\Models\Workflow;

interface WorkflowInterface
{
    public static function updateReadStatus(Workflow $workflow);
}
