@php
$buttonUrl = \App\Http\Classes\Notifier::url('admin.conference.applications.show', ['application' => $application->uuid]);
$money = $application->cost_currency.' '.number_format($application->total_cost, 2);
$dates = optional($application->conference_start_date)->format('F j, Y').($application->conference_end_date ? ' to '.$application->conference_end_date->format('F j, Y') : '');
@endphp
<x-mail::message>
# New conference grant application

A staff member has submitted an application for **{{ $application->call->title }}**.

<x-mail::panel>
**Applicant:** {{ $application->candidate_name }}  
**Department:** {{ $application->department }}  
**Category:** {{ config('conference.staff_categories.'.$application->staff_category, '-') }}  
**Beneficiary:** {{ $application->previously_sponsored ? 'Previous TETFund beneficiary' : 'First-time beneficiary' }}  
**{{ $application->event_noun }}:** {{ $application->conference_title }}  
**Dates:** {{ $dates }}  
**Total requested:** {{ $money }}
</x-mail::panel>

<x-mail::button :url="$buttonUrl">
Review Application
</x-mail::button>

{{ config('app.name') }} - DRIP
</x-mail::message>
