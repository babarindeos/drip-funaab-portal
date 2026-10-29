@php
$buttonUrl = \App\Http\Classes\Notifier::url('staff.conference.applications.show', ['application' => $application->uuid]);
$money = $application->cost_currency.' '.number_format($application->total_cost, 2);
$dates = optional($application->conference_start_date)->format('F j, Y').($application->conference_end_date ? ' to '.$application->conference_end_date->format('F j, Y') : '');
@endphp
<x-mail::message>
# Application received

Dear {{ $application->candidate_name }},

Thank you. We have received your application for **{{ $application->call->title }}**.

<x-mail::panel>
**{{ $application->event_noun }}:** {{ $application->conference_title }}  
**Venue:** {{ $application->conference_venue }}  
**Dates:** {{ $dates }}  
**Total requested:** {{ $money }}  
**Submitted:** {{ optional($application->submitted_at)->format('l, F jS Y, g:i A') }}
</x-mail::panel>

Your application will now be reviewed by the University Staff Development Committee. We will email you the outcome, and you can follow its progress on your application page.

<x-mail::button :url="$buttonUrl">
View My Application
</x-mail::button>

Thanks,  
{{ config('app.name') }} - DRIP
</x-mail::message>
