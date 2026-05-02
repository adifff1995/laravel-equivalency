@extends('layouts.app')

@section('title', 'Edit Request #' . $request->id)
@section('page-title', 'Edit Request')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('admin.requests.show', $request) }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 fw-600" style="color:#1a3c5e">Edit Request #{{ $request->id }}</h5>
                <small class="text-muted">{{ $request->name }} — {{ $request->student_id }}</small>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.requests.update', $request) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    @include('admin.requests._form', ['types' => $types, 'request' => $request])
                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Update Request
                        </button>
                        <a href="{{ route('admin.requests.show', $request) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
