<x-mail::message>
{{ $proposalReviewer->message }}

<x-mail::button :url="$proposalReviewer->review_link">
Open Review Form
</x-mail::button>
</x-mail::message>
