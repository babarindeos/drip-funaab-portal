<x-admin-layout>
    @php $call = $call_for_proposal; @endphp
    <div class="drip-page">
        <div class="drip-container">
            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('admin.call_for_proposals.show', ['call_for_proposal' => $call->id]) }}">{{ \Illuminate\Support\Str::limit($call->title, 40) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Edit</span>
            </nav>
            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Edit call</h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>{{ $call->title }}</span></div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.call_for_proposals.update', ['call_for_proposal' => $call->id]) }}" enctype="multipart/form-data" style="margin-top:28px;">
                @csrf
                @include('admin.call_for_proposals._form', ['call' => $call, 'submit_label' => 'Save changes'])
            </form>
        </div>
    </div>
</x-admin-layout>
