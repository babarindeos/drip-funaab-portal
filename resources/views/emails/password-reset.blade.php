<x-mail::message>
# Set a new password

Dear {{ trim($user->firstname.' '.$user->surname) ?: 'colleague' }},

We received a request to set a new password for the DRIP account that uses **{{ $user->email }}**.

<x-mail::button :url="$url">
Set a New Password
</x-mail::button>

This link works for {{ $minutes }} minutes and can be used once.

If you did not ask for this, you can ignore this email: your password stays as it is.

Yours faithfully,

**The DRIP Portal Team**<br>
Directorate of Research, Innovations and Partnerships<br>
Federal University of Agriculture, Abeokuta

<x-slot:subcopy>
If the button does not work, copy this address into your browser: [{{ $url }}]({{ $url }})
</x-slot:subcopy>
</x-mail::message>
