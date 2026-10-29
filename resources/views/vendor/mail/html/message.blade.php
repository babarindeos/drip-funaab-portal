<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('drip.base_url')">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
**Directorate of Research, Innovations and Partnerships (DRIP)**<br>
Federal University of Agriculture, Abeokuta, Ogun State, Nigeria

This is an automated message from the [DRIP Portal]({{ config('drip.base_url') }}). Please do not reply to it directly.

© {{ date('Y') }} DRIP, FUNAAB. All rights reserved.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
