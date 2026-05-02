@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card card shadow-lg">
        <div class="auth-logo">
            <i class="bi bi-mortarboard-fill" style="font-size:2.5rem;color:#e8a820"></i>
            <h4 class="mt-2 mb-0 fw-700">IT Faculty</h4>
            <p class="mb-0 opacity-75" style="font-size:0.85rem">Course Equivalency System</p>
        </div>

        <div class="card-body p-4">
            <h5 class="fw-600 mb-1" style="color:#1a3c5e">Staff Sign In</h5>
            <p class="text-muted mb-4" style="font-size:0.85rem">Enter your institutional credentials</p>

            @if ($errors->any())
                <div class="alert alert-danger alert-sm py-2" style="font-size:0.85rem">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-500" style="font-size:0.85rem">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="you@university.edu" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-500" style="font-size:0.85rem">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label" style="font-size:0.85rem">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-500">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>

            <hr class="my-3">
            <div class="text-center">
                <a href="{{ route('requests.public.create') }}" class="text-muted" style="font-size:0.8rem">
                    <i class="bi bi-file-earmark-plus me-1"></i>Submit a request as a student
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
