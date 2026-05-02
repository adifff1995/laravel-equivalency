@extends('layouts.app')

@section('title', 'Request Submitted')

@section('content')
<div style="background: linear-gradient(135deg, #1a3c5e 0%, #2c6fad 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 2rem 1rem;">
    <div style="max-width:560px;width:100%">

        {{-- Success card --}}
        <div class="card shadow-lg" style="border-radius:16px;overflow:hidden">

            {{-- Green top banner --}}
            <div class="text-center py-4" style="background:linear-gradient(135deg,#198754,#20c997)">
                <div style="width:72px;height:72px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                    <i class="bi bi-check-lg text-white" style="font-size:2.2rem"></i>
                </div>
                <h4 class="text-white fw-700 mb-1">Request Submitted!</h4>
                <p class="text-white mb-0" style="opacity:.85;font-size:.9rem">
                    Thank you, <strong>{{ $studentName }}</strong>
                </p>
            </div>

            <div class="card-body p-4">

                {{-- Tracking code display --}}
                <div class="text-center mb-4">
                    <p class="text-muted mb-2" style="font-size:.85rem">Your unique tracking code is:</p>
                    <div id="trackingCodeBox" class="d-inline-block px-4 py-3 rounded-3 position-relative"
                         style="background:#f0f7ff;border:2px dashed #2c6fad;cursor:pointer"
                         onclick="copyCode()" title="Click to copy">
                        <span class="mono fw-700" style="font-size:1.6rem;letter-spacing:.12em;color:#1a3c5e">
                            {{ $trackingCode }}
                        </span>
                        <div class="mt-1" style="font-size:.72rem;color:#2c6fad">
                            <i class="bi bi-clipboard me-1"></i><span id="copyHint">Click to copy</span>
                        </div>
                    </div>
                </div>

                {{-- Warning box --}}
                <div class="alert alert-warning d-flex gap-2 py-2 mb-4" style="font-size:.84rem">
                    <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0" style="color:#fd7e14"></i>
                    <div>
                        <strong>Important:</strong> Please save this tracking code. You will need it to check your request status. We do not store personal contact info for lookups unless you provided an email.
                    </div>
                </div>

                {{-- What happens next --}}
                <h6 class="fw-700 mb-3" style="color:#1a3c5e;font-size:.85rem;text-transform:uppercase;letter-spacing:.07em">
                    What happens next?
                </h6>
                <div class="d-flex flex-column gap-2 mb-4">
                    @foreach([
                        ['bi-hourglass-split','text-secondary','Your request is currently <strong>New</strong> and will be reviewed by faculty staff.'],
                        ['bi-search','text-primary','Staff will review your documents and update the status.'],
                        ['bi-bell','text-success','Once a decision is made, the status will change to <strong>Approved</strong> or <strong>Rejected</strong>.'],
                        ['bi-cursor','text-info','You can track your request anytime using your tracking code.'],
                    ] as [$icon, $color, $text])
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi {{ $icon }} {{ $color }} mt-1" style="font-size:1rem;flex-shrink:0"></i>
                        <span style="font-size:.875rem">{!! $text !!}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Action buttons --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('requests.track') }}" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-2"></i>Track My Request
                    </a>
                    <a href="{{ route('requests.public.create') }}" class="btn btn-outline-secondary flex-fill">
                        <i class="bi bi-plus me-2"></i>New Request
                    </a>
                </div>

            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-white-50" style="font-size:.8rem">
                Staff login →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyCode() {
    const code = '{{ $trackingCode }}';
    navigator.clipboard.writeText(code).then(() => {
        document.getElementById('copyHint').textContent = '✓ Copied!';
        setTimeout(() => document.getElementById('copyHint').textContent = 'Click to copy', 2000);
    }).catch(() => {
        // Fallback for older browsers
        const el = document.createElement('textarea');
        el.value = code;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        document.getElementById('copyHint').textContent = '✓ Copied!';
        setTimeout(() => document.getElementById('copyHint').textContent = 'Click to copy', 2000);
    });
}
</script>
@endpush
