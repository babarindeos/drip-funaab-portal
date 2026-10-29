<x-admin-layout>
    <div class="drip-page">
        <div class="drip-container">
            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.reviewers.index') }}">Reviewers</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Edit reviewer</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Edit reviewer</h1>
                    <div class="drip-subtitle"><x-icon name="users" :size="20" /> <span>{{ $reviewer->name }}</span></div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.reviewers.update', ['reviewer' => $reviewer->id]) }}" style="margin-top:28px;">
                @csrf
                @include('admin.reviewers._form', ['reviewer' => $reviewer, 'submit_label' => 'Save changes'])
            </form>
        </div>
    </div>
</x-admin-layout>
