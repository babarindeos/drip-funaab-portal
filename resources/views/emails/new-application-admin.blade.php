@php
$buttonUrl = \App\Http\Classes\Notifier::url('admin.call_for_proposals.proposal_application', ['call_for_proposal' => $application->call_for_proposal_id, 'proposal_application' => $application->id]);
@endphp
<x-mail::message>
# New application received

A staff member has submitted an application for **{{ $application->call_for_proposal->title }}**.

<x-mail::panel>
**Applicant:** {{ $application->owner->surname }} {{ $application->owner->firstname }}  
**Proposal:** {{ $application->proposal_title }}  
**Principal Investigator:** {{ $application->principal_investigator }}  
**Submitted:** {{ $application->created_at->format('l, F jS Y, g:i A') }}
</x-mail::panel>

<x-mail::button :url="$buttonUrl">
Open Application
</x-mail::button>

{{ config('app.name') }} - DRIP
</x-mail::message>
