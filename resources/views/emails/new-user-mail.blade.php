@php
$first_name = trim($payload['firstname'] ?? '') ?: (trim($payload['fullname'] ?? '') ?: 'Colleague');
$sign_in_url = rtrim(config('drip.base_url'), '/').'/#signin';
@endphp
<x-mail::message>
# Welcome to the DRIP Portal

Dear {{ $first_name }},

Your account on the **DRIP Portal** of the Directorate of Research, Innovations and Partnerships, Federal University of Agriculture, Abeokuta, has been created successfully.

Through the portal you can apply for **Institution-Based Research (IBR) grants** and **TETFund Conference/Workshop sponsorship**, and follow the progress of your applications.

<x-mail::panel>
**Your sign-in details**

**Email address:** {{ $payload['username'] }}<br>
**Temporary password:** {{ $payload['password'] }}
</x-mail::panel>

<x-mail::button :url="$sign_in_url">
Sign in to the DRIP Portal
</x-mail::button>

## Getting started

1. Sign in with the email address and temporary password above.
2. Complete your profile: your designation and phone number are pre-filled on your applications.
3. Change your password from **My profile → Change password** to one only you know.

## Keeping your account secure

Your temporary password was generated for you and is known only to you. DRIP staff will never ask for your password. If you did not register for this account, please contact the Directorate so it can be removed.

Yours faithfully,

**The DRIP Portal Team**<br>
Directorate of Research, Innovations and Partnerships<br>
Federal University of Agriculture, Abeokuta

<x-slot:subcopy>
If the button does not work, copy this address into your browser: [{{ $sign_in_url }}]({{ $sign_in_url }})
</x-slot:subcopy>
</x-mail::message>
