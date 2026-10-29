@php
$buttonUrl = \App\Http\Classes\Notifier::url('admin.call_for_proposals.proposal_application', ['call_for_proposal' => $application->call_for_proposal_id, 'proposal_application' => $application->id]);
@endphp
<x-mail::message>
# All reviews are in

Every reviewer assigned to this proposal has submitted their scores. It is ready for a decision.

<x-mail::panel>
**Proposal:** {{ $application->proposal_title }}  
**Call:** {{ $application->call_for_proposal->title }}  
**Reviewers:** {{ $application->reviewers_completed_count }} of {{ $application->reviewers_assigned_count }} completed  
**Average score:** {{ $application->average_score }}
</x-mail::panel>

<x-mail::button :url="$buttonUrl">
Review and Decide
</x-mail::button>

{{ config('app.name') }} - DRIP
</x-mail::message>
