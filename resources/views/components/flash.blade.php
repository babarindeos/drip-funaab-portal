@php
    // One place that shows the portal's messages, whichever way a controller sent them:
    //   ->with('success', 'text')                          a success message
    //   ->with('error', 'text')                            an error message
    //   ->with(['error' => true, 'status' => 'success'|'fail', 'message' => 'text'])   (older controllers)
    $messages = [];

    if (session('success')) {
        $messages[] = ['success', session('success')];
    }
    if (session('error') && session('error') !== true) {
        $messages[] = ['error', session('error')];
    }
    if (session('error') === true && session('message')) {
        $messages[] = [session('status') === 'success' ? 'success' : 'error', session('message')];
    }
@endphp
@foreach ($messages as [$type, $text])
    <div class="drip-alert drip-alert--{{ $type }}">
        <x-icon :name="$type === 'success' ? 'circle-check' : 'circle-x'" :size="18" />
        <span>{{ $text }}</span>
    </div>
@endforeach
