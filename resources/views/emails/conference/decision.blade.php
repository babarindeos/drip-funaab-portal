@php
$buttonUrl = \App\Http\Classes\Notifier::url('staff.conference.applications.show', ['application' => $application->uuid]);
$approved = $application->status === \App\Models\Conference\ConferenceApplication::STATUS_APPROVED;
// keep the line breaks the admin typed (two spaces + newline is a line break in markdown)
$breaks = fn ($text) => str_replace(["\r\n", "\n"], "  \n", trim((string) $text));
@endphp
<x-mail::message>
# Update on your application

Dear {{ $application->candidate_name }},

@if ($approved)
We are pleased to inform you that your application for **{{ $application->call->title }}** has been **approved**.
@else
Thank you for applying. After review by the committee, your application for **{{ $application->call->title }}** was **not approved** on this occasion.
@endif

<x-mail::panel>
**{{ $application->event_noun }}:** {{ $application->conference_title }}  
**Status:** {{ $application->status_label }}
</x-mail::panel>

@if ($approved && filled($application->call->approval_instructions))
**Next steps**

{{ $breaks($application->call->approval_instructions) }}

@endif
@if ($application->decision_remark_shared && filled($application->decision_remark))
**Message from DRIP**

{{ $breaks($application->decision_remark) }}

@endif
<x-mail::button :url="$buttonUrl">
View My Application
</x-mail::button>

Thanks,  
{{ config('app.name') }} - DRIP
</x-mail::message>
