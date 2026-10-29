<x-staff-layout>
    @php
        $definitions = config('conference.document_types');
        $size = fn ($bytes) => $bytes >= 1048576 ? round($bytes / 1048576, 1).' MB' : max(1, round($bytes / 1024)).' KB';
        $edit_url = route('staff.conference.applications.edit', ['application' => $application->uuid]);
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('staff.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('staff.conference.calls.show', ['call' => $call->uuid]) }}">{{ \Illuminate\Support\Str::limit($call->title, 40) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Documents and submission</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Documents and submission <x-status-badge :status="$application->status" /></h1>
                    <div class="drip-subtitle"><x-icon name="graduation-cap" :size="20" /> <span>{{ $call->title }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ $edit_url }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Back to the form</a>
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif

            @if (! $application->staff_category)
                <div class="drip-alert drip-alert--error">
                    <x-icon name="info" :size="18" />
                    <span>Choose your staff category on <a href="{{ $edit_url }}" class="drip-clear">the form</a> first. The documents you need depend on it.</span>
                </div>
            @endif

            <div class="drip-stack" style="margin-top:28px;">

                {{-- documents --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Documents</h2>
                        <p class="drip-form-section__sub">Upload each document below. Uploading again replaces the earlier file.</p>

                        @forelse ($types as $type)
                            @php
                                $definition = $definitions[$type];
                                $document = $application->document($type);
                                $bag = $errors->getBag('upload_'.$type);
                                $accept = '.'.implode(',.', $definition['extensions']);
                            @endphp
                            <div class="drip-doc-row">
                                <div class="drip-doc-row__info">
                                    <div class="drip-doc-row__title">
                                        <span class="{{ $document ? 'is-ok' : 'is-wait' }}"><x-icon :name="$document ? 'circle-check' : 'clock'" :size="18" /></span>
                                        {{ $definition['label'] }}
                                    </div>
                                    <div class="drip-help">{{ $definition['help'] }}</div>

                                    @if ($document)
                                        <div class="drip-doc-row__file">
                                            <x-icon name="file-text" :size="15" />
                                            <span>{{ $document->original_name }} &middot; {{ $size($document->size) }} &middot; uploaded {{ $document->updated_at->format('M j, Y') }}</span>
                                            <a href="{{ route('conference.files.document', ['application' => $application->uuid, 'type' => $type]) }}" target="_blank" class="drip-clear">View</a>
                                        </div>
                                    @endif

                                    @if ($bag->has('file'))
                                        <div class="drip-error">{{ $bag->first('file') }}</div>
                                    @endif
                                </div>

                                <div class="drip-doc-row__actions">
                                    <form method="POST" action="{{ route('staff.conference.documents.store', ['application' => $application->uuid, 'type' => $type]) }}" enctype="multipart/form-data" class="drip-upload">
                                        @csrf
                                        <input type="file" name="file" accept="{{ $accept }}" class="drip-control" required aria-label="{{ $definition['label'] }}">
                                        <button type="submit" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="upload" :size="15" /> {{ $document ? 'Replace' : 'Upload' }}</button>
                                    </form>

                                    @if ($document)
                                        <form method="POST" action="{{ route('staff.conference.documents.destroy', ['application' => $application->uuid, 'type' => $type]) }}" onsubmit="return confirm('Remove this document?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="drip-btn drip-btn--danger drip-btn--sm"><x-icon name="trash" :size="15" /> Remove</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="drip-help">Choose your staff category on the form to see which documents you need.</p>
                        @endforelse
                    </div>
                </div>

                {{-- ready to submit? --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Ready to submit?</h2>
                        <p class="drip-form-section__sub">Everything below must be in place. After you submit, you cannot change your application.</p>

                        <ul class="drip-checklist">
                            <li class="{{ $issues['call_closed'] ? 'is-fail' : 'is-pass' }}">
                                <x-icon :name="$issues['call_closed'] ? 'circle-x' : 'circle-check'" :size="18" />
                                <span>The call is open<small>{{ $issues['call_closed'] ? 'The deadline has passed, so this application can no longer be submitted.' : 'The deadline is '.$call->close_date->format('F j, Y').'.' }}</small></span>
                            </li>

                            <li class="{{ empty($issues['fields']) ? 'is-pass' : 'is-fail' }}">
                                <x-icon :name="empty($issues['fields']) ? 'circle-check' : 'circle-x'" :size="18" />
                                <span>The form is fully filled in
                                    @if (! empty($issues['fields']))
                                        <small>{{ count($issues['fields']) }} required {{ \Illuminate\Support\Str::plural('field', count($issues['fields'])) }} still empty: {{ implode(', ', $issues['fields']) }}. <a href="{{ $edit_url }}" class="drip-clear">Go to the form</a></small>
                                    @endif
                                </span>
                            </li>

                            <li class="{{ empty($issues['rules']) ? 'is-pass' : 'is-fail' }}">
                                <x-icon :name="empty($issues['rules']) ? 'circle-check' : 'circle-x'" :size="18" />
                                <span>Your {{ strtolower($application->event_noun) }} meets the rules
                                    @foreach ($issues['rules'] as $rule)
                                        <small>{{ $rule['label'] }}@if (! empty($rule['message'])) - {{ $rule['message'] }}@endif</small>
                                    @endforeach
                                </span>
                            </li>

                            <li class="{{ empty($issues['documents']) ? 'is-pass' : 'is-fail' }}">
                                <x-icon :name="empty($issues['documents']) ? 'circle-check' : 'circle-x'" :size="18" />
                                <span>All documents are uploaded
                                    @if (! empty($issues['documents']))
                                        <small>Still needed: {{ implode(', ', $issues['documents']) }}.</small>
                                    @endif
                                </span>
                            </li>

                            <li class="{{ $issues['declaration'] ? 'is-fail' : 'is-pass' }}">
                                <x-icon :name="$issues['declaration'] ? 'circle-x' : 'circle-check'" :size="18" />
                                <span>The declaration is ticked
                                    @if ($issues['declaration'])
                                        <small>Tick the declaration at the end of <a href="{{ $edit_url }}" class="drip-clear">the form</a> and save.</small>
                                    @endif
                                </span>
                            </li>
                        </ul>

                        <form method="POST" action="{{ route('staff.conference.applications.submit', ['application' => $application->uuid]) }}" style="margin-top:24px;"
                              onsubmit="return confirm('Submit your application? You will not be able to change it afterwards.');">
                            @csrf
                            <button type="submit" class="drip-btn drip-btn--primary" @disabled(! $issues['ok']) @if (! $issues['ok']) style="opacity:.5; cursor:not-allowed;" @endif>
                                <x-icon name="send" :size="18" /> Submit application
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-staff-layout>
