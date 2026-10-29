<x-admin-layout>
    <div class="drip-page">
        <div class="drip-container">
            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.reviewers.index') }}">Reviewers</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Add reviewer</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Add reviewer</h1>
                    <div class="drip-subtitle"><x-icon name="users" :size="20" /> <span>People who score proposals for the Call for Proposals</span></div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.reviewers.store') }}" style="margin-top:28px;">
                @csrf
                @include('admin.reviewers._form', ['reviewer' => $reviewer, 'submit_label' => 'Add reviewer'])
            </form>
        </div>
    </div>
</x-admin-layout>
