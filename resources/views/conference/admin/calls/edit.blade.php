<x-admin-layout>
    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Edit call</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Edit call</h1>
                    <div class="drip-subtitle">
                        <x-icon name="graduation-cap" :size="20" />
                        <span>{{ $call->title }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.conference.calls.update', ['call' => $call->id]) }}" enctype="multipart/form-data" style="margin-top:28px;">
                @csrf
                @method('PUT')
                @include('conference.admin.calls._form', ['call' => $call, 'submit_label' => 'Save changes'])
            </form>

        </div>
    </div>
</x-admin-layout>
