<x-admin-layout>
    @php $call = new \App\Models\CallForProposal(); @endphp
    <div class="drip-page">
        <div class="drip-container">
            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">New call</span>
            </nav>
            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">New call</h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>Set the dates for a research proposal call</span></div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.call_for_proposals.store') }}" enctype="multipart/form-data" style="margin-top:28px;">
                @csrf
                @include('admin.call_for_proposals._form', ['call' => $call, 'submit_label' => 'Create call'])
            </form>
        </div>
    </div>
</x-admin-layout>
