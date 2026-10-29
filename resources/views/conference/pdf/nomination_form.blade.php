<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>TETFund Conference Attendance Nomination Form</title>
    <style>
        @page { margin: 22px 30px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #111; }
        .center { text-align: center; }
        .brand { font-size: 13px; font-weight: bold; letter-spacing: 1px; }
        .banner { background: #cc1f1f; color: #fff; font-weight: bold; font-size: 12px; padding: 5px 8px; margin: 6px 0 3px; text-align: center; }
        .sub { font-weight: bold; font-size: 10px; margin-bottom: 8px; }
        table { border-collapse: collapse; width: 100%; }
        .top td { vertical-align: top; }
        .roman td { padding: 3px 0; }
        .photo { border: 1px solid #333; width: 100px; height: 118px; text-align: center; vertical-align: middle; font-size: 7.5px; font-weight: bold; }
        .photo img { width: 98px; height: 116px; }
        .grid td, .grid th { border: 1px solid #333; padding: 5px 6px; vertical-align: top; }
        .grid th { background: #0b8f3d; color: #fff; font-size: 10px; text-align: center; }
        .grid .sn { width: 4%; font-weight: bold; text-align: center; }
        .grid .item { width: 24%; font-weight: bold; }
        .line { padding: 2px 0; }
        .val { font-weight: bold; border-bottom: 1px dotted #777; }
        .sign td { width: 50%; padding-top: 34px; vertical-align: bottom; }
        .sign .rule { border-top: 1px dotted #333; padding-top: 3px; font-weight: bold; }
        .note { margin-top: 10px; color: #cc1f1f; font-weight: bold; text-align: center; }
        .foot { margin-top: 8px; color: #777; font-size: 7.5px; text-align: center; }
    </style>
</head>
<body>
    @php
        $box = fn ($on) => $on ? '&#9746;' : '&#9744;';          // checked / empty box
        $d = fn ($value, $format = 'j F Y') => $value ? $value->format($format) : '';
        $n = fn ($value) => is_null($value) ? '' : number_format((float) $value, 2);
        $currency = $application->cost_currency;
        $teaching = $application->staff_category === 'teaching';
    @endphp

    <div class="center brand">TERTIARY EDUCATION TRUST FUND</div>
    <div class="banner">TETFund CONFERENCE ATTENDANCE NOMINATION FORM</div>
    <div class="center sub">(To be completed by Candidate and Beneficiary Institution)</div>

    <table class="top">
        <tr>
            <td>
                <table class="roman">
                    <tr><td width="26">I.</td><td width="180">Name of Beneficiary Institution:</td><td class="val">{{ $application->institution }}</td></tr>
                    <tr><td>ii.</td><td>Name of Candidate/Staff:</td><td class="val">{{ $application->candidate_name }}</td></tr>
                    <tr><td>iii.</td><td>Gender:</td><td>Male {!! $box($application->gender === 'male') !!} &nbsp;&nbsp; Female {!! $box($application->gender === 'female') !!}</td></tr>
                    <tr><td>iv.</td><td>Date of Birth:</td><td class="val">{{ $d($application->date_of_birth) }}</td></tr>
                    <tr><td>v.</td><td>Present Position (For Principal Officers):</td><td class="val">{{ $application->present_position }}</td></tr>
                    <tr><td>vi.</td><td>Staff Category:</td><td>Teaching Staff {!! $box($teaching) !!} &nbsp;&nbsp; Non Teaching Staff {!! $box($application->staff_category === 'non_teaching') !!}</td></tr>
                    <tr><td>vii.</td><td>Phone Number / Signature:</td><td class="val">{{ $application->phone }}</td></tr>
                </table>
            </td>
            <td width="112" align="right">
                <table class="photo-wrap"><tr><td class="photo">
                    @if ($photo)<img src="{{ $photo }}" alt="">@else CANDIDATE'S<br>PASSPORT<br>PHOTOGRAPH @endif
                </td></tr></table>
            </td>
        </tr>
    </table>

    <table class="grid" style="margin-top:8px;">
        <tr><th width="4%">S/N</th><th colspan="2">CANDIDATE'S DATA/INSTITUTIONAL RECORD</th></tr>
        <tr><td class="sn">1.</td><td class="item">Department</td><td>{{ $application->department }}</td></tr>
        <tr><td class="sn">2.</td><td class="item">Qualification</td><td>Highest Qualification: <span class="val">{{ $application->highest_qualification }}</span> &nbsp; Area of Specialisation: <span class="val">{{ $application->area_of_specialisation }}</span></td></tr>
        <tr><td class="sn">3.</td><td class="item">Rank</td><td>{{ $application->staff_rank }}</td></tr>
        <tr><td class="sn">4.</td><td class="item">Grade Level</td><td>{{ $application->grade_level }}</td></tr>
        <tr><td class="sn">5.</td><td class="item">Date of 1st Appointment</td><td>{{ $d($application->first_appointment_date) }}</td></tr>
        <tr><td class="sn">6.</td><td class="item">Duration of Entire Work Experience</td><td>{{ is_null($application->work_experience_years) ? '' : $application->work_experience_years.' years' }}</td></tr>
        <tr><td class="sn">7.</td><td class="item">Number of Years Spent in the Institution</td><td>{{ is_null($application->years_in_institution) ? '' : $application->years_in_institution.' years' }}</td></tr>
        <tr>
            <td class="sn">8.</td><td class="item">Conference to be Attended</td>
            <td>
                <div><strong>Conference Details:</strong></div>
                <div class="line">a) Conference Organiser(s): <span class="val">{{ $application->conference_organiser }}</span></div>
                <div class="line">b) Title &amp; Venue: <span class="val">{{ $application->conference_title }}{{ $application->conference_venue ? ', '.$application->conference_venue : '' }}</span></div>
                <div class="line">c) Registration Fee: <span class="val">{{ $application->registration_fee === null ? '' : $currency.' '.$n($application->registration_fee) }}</span></div>
                <div class="line">d) Date: <span class="val">{{ $d($application->conference_start_date) }}{{ $application->conference_end_date ? ' to '.$d($application->conference_end_date) : '' }}</span> &nbsp; Duration: <span class="val">{{ $application->duration_days ? $application->duration_days.' day(s)' : '' }}</span></div>
                <div class="line">Mode: <span class="val">{{ ucfirst((string) $application->conference_scope) }}, {{ str_replace('_', ' ', (string) $application->conference_mode) }}@if ($teaching), {{ $application->presentation_type }} presentation @endif</span></div>
                <div class="line">Other costs ({{ $currency }}): flight {{ $n($application->flight_cost) ?: '-' }}, accommodation {{ $n($application->accommodation_cost) ?: '-' }}, feeding {{ $n($application->feeding_cost) ?: '-' }}, excursion {{ $n($application->excursion_cost) ?: '-' }}</div>
            </td>
        </tr>
        <tr>
            <td class="sn">9.</td><td class="item">Conference Attendance History</td>
            <td>
                <div><strong>Last International Conference Attended (Sponsored by TETFund):</strong></div>
                @if ($application->previously_sponsored)
                    <div class="line">a) Title: <span class="val">{{ $application->last_conference_title }}</span></div>
                    <div class="line">b) Date: <span class="val">{{ $d($application->last_conference_date) }}</span> &nbsp; Venue: <span class="val">{{ $application->last_conference_venue }}</span></div>
                    <div class="line">c) Publication from the conference presentation (as cited): <span class="val">{{ $application->last_conference_publication }}</span></div>
                @else
                    <div class="line">None. This is the candidate's first TETFund-sponsored conference.</div>
                @endif
            </td>
        </tr>
        <tr>
            <td class="sn">10.</td><td class="item">Candidate's Salary Account Details</td>
            <td>
                <div class="line">a) Bank Name &amp; Branch: <span class="val">{{ $application->bank_name }}{{ $application->bank_branch ? ', '.$application->bank_branch : '' }}</span></div>
                <div class="line">b) Account Name: <span class="val">{{ $application->account_name }}</span></div>
                <div class="line">c) Account Number: <span class="val">{{ $application->secret('account_number') }}</span></div>
                <div class="line">d) Sort Code: <span class="val">{{ $application->secret('sort_code') }}</span></div>
            </td>
        </tr>
    </table>

    <table class="sign">
        <tr>
            <td><div class="rule">Name &amp; Signature of Vice Chancellor/Rector/Provost<br><em style="font-weight:normal;">(Including Stamp)</em></div></td>
            <td><div class="rule">Name &amp; Signature of Director Academic Planning/DVC<br><em style="font-weight:normal;">(Academics)(Including Stamp)</em></div></td>
        </tr>
    </table>

    <div class="note">Note: Any form that is not properly filled will be automatically disqualified</div>
    <div class="foot">DRIP reference {{ strtoupper(substr($application->uuid, 0, 8)) }} &middot; {{ $call->title }} &middot; printed {{ $printed_at->format('j F Y, g:i A') }}</div>
</body>
</html>
