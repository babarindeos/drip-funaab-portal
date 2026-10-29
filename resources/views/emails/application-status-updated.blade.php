@php
$buttonUrl = \App\Http\Classes\Notifier::url('staff.call_for_proposals.application', ['uuid' => $application->call_for_proposal->uuid]);
@endphp
<x-mail::message>
# Update on your application

Dear {{ $application->owner->firstname }},

@if ($application->status === 'accepted')
We are pleased to inform you that your application for **{{ $application->call_for_proposal->title }}** has been **accepted**.
@elseif ($application->status === 'rejected')
Thank you for applying. After review, your application for **{{ $application->call_for_proposal->title }}** was **not successful** on this occasion.
@else
Your application for **{{ $application->call_for_proposal->title }}** has been **acknowledged** and is now under consideration.
@endif

<x-mail::panel>
**Proposal:** {{ $application->proposal_title }}  
**Status:** {{ ucfirst($application->status) }}
</x-mail::panel>

@if ($shareRemark && filled($application->remark))
**Remark from DRIP:**

{{ $application->remark }}
@endif

<x-mail::button :url="$buttonUrl">
View My Application
</x-mail::button>

Thanks,  
{{ config('app.name') }} - DRIP
</x-mail::message>
