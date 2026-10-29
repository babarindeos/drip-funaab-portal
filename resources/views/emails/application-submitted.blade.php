@php
$buttonUrl = \App\Http\Classes\Notifier::url('staff.call_for_proposals.application', ['uuid' => $application->call_for_proposal->uuid]);
@endphp
<x-mail::message>
# Application received

Dear {{ $application->owner->firstname }},

Thank you. We have received your application for **{{ $application->call_for_proposal->title }}**.

<x-mail::panel>
**Proposal:** {{ $application->proposal_title }}  
**Principal Investigator:** {{ $application->principal_investigator }}  
**Submitted:** {{ $application->created_at->format('l, F jS Y, g:i A') }}  
**Status:** Pending
</x-mail::panel>

We will email you again when there is an update on your application.

<x-mail::button :url="$buttonUrl">
View My Application
</x-mail::button>

Thanks,  
{{ config('app.name') }} - DRIP
</x-mail::message>
