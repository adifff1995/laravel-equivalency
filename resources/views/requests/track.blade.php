@extends('layouts.app')

@section('title', 'Track Your Request')

@section('content')
<div style="background: linear-gradient(135deg, #1a3c5e 0%, #2c6fad 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 2rem 1rem;">
    <div style="max-width:520px;width:100%">

        <div class="text-center text-white mb-4">
            <i class="bi bi-mortarboard-fill" style="font-size:2.2rem;color:#e8a820"></i>
            <h3 class="mt-2 fw-700">Track Your Request</h3>
            <p class="opacity-75" style="font-size:.9rem">IT Faculty — Course Equivalency System</p>
        </div>

        <div class="card shadow-lg" style="border-radius:14px;overflow:hidden">
            <div class="card-header" style="background:#1a3c5e;color:#fff;padding:1.1rem 1.5rem">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-search" style="color:#e8a820"></i>
                    <span class="fw-600">Enter Your Tracking Code</span>
                </div>
            </div>

            <div class="card-body p-4">

                @if($errors->any())
                <div class="alert alert-danger d-flex gap-2 py-2 mb-4" style="font-size:.87rem">
                    <i class="bi bi-exclamation-circle-fill mt-1 flex-shrink-0"></i>
                    <div>{{ $errors->first('tracking_code') }}</div>
                </div>
                @endif

                <form method="POST" action="{{ route('requests.track.lookup') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-600" style="color:#1a3c5e">Tracking Code</label>
                        <input type="text"
                               name="tracking_code"
                               value="{{ old('tracking_code') }}"
                               class="form-control form-control-lg text-center mono @error('tracking_code') is-invalid @enderror"
                               placeholder="EQ-XXXXXXXX"
                               style="font-size:1.3rem;letter-spacing:.1em;text-transform:uppercase"
                               maxlength="20"
                               autocomplete="off"
                               autofocus>
                        <div class="form-text text-center mt-2">
                            The tracking code was shown after you submitted your request.<br>
                            It looks like: <span class="mono fw-500">EQ-A3F92B17</span>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-500">
                            <i class="bi bi-search me-2"></i>Find My Request
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="text-muted mb-3" style="font-size:.83rem">Don't have a tracking code yet?</p>
                    <a href="{{ route('requests.public.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-plus me-1"></i> Submit a New Request
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
    // Auto-uppercase and format the tracking code input
    document.querySelector('input[name="tracking_code"]').addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
