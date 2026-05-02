@extends('layouts.app')

@section('title', 'All Requests')
@section('page-title', 'Course Equivalency Requests')

@section('content')

{{-- ── Stats Row ── --}}
<div class="row g-3 mb-4">
    @php
        $statusCounts = \App\Models\EquivalencyRequest::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status');
        $statColors = ['new'=>'secondary','under_review'=>'primary','ready_for_entry'=>'warning','entered'=>'info','approved'=>'success','rejected'=>'danger'];
    @endphp
    @foreach(\App\Models\EquivalencyRequest::statusLabels() as $key => $label)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center py-3">
            <div class="h3 mb-0 fw-700" style="color:#1a3c5e">{{ $statusCounts[$key] ?? 0 }}</div>
            <div class="small text-muted mt-1">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Filters ── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.requests.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-500 mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           class="form-control border-start-0"
                           placeholder="Student name or ID…">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-500 mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $val => $lbl)
                        <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-500 mb-1">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($types as $val => $lbl)
                        <option value="{{ $val }}" {{ $type === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Table ── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-2"></i>Requests
            <span class="badge bg-secondary ms-2">{{ $requests->total() }}</span>
        </span>
        <a href="{{ route('admin.requests.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i> New Request
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
            No requests found.
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tracking Code</th>
                    <th>Student</th>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Major</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                <tr>
                    <td class="text-muted mono" style="font-size:0.8rem">{{ $req->id }}</td>
                    <td>
                        <span class="mono small fw-500" style="color:#1a3c5e">{{ $req->tracking_code }}</span>
                    </td>
                    <td class="fw-500">{{ $req->name }}</td>
                    <td class="mono">{{ $req->student_id }}</td>
                    <td>
                        <span class="badge bg-light text-dark border" style="font-weight:500">
                            {{ $req->type_label }}
                        </span>
                    </td>
                    <td>{{ $req->major }}</td>
                    <td>
                        <span class="badge {{ $req->status_badge_class }}">
                            {{ $req->status_label }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $req->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.requests.show', $req) }}"
                               class="btn btn-sm btn-outline-secondary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.requests.edit', $req) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            {{-- Quick status buttons --}}
                            @if($req->status === 'new')
                                <form method="POST" action="{{ route('admin.requests.change-status', $req) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="under_review">
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark Under Review">
                                        <i class="bi bi-arrow-right-circle"></i>
                                    </button>
                                </form>
                            @elseif($req->status === 'under_review')
                                <form method="POST" action="{{ route('admin.requests.change-status', $req) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="ready_for_entry">
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Mark Ready for Entry">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                            @elseif($req->status === 'ready_for_entry')
                                <form method="POST" action="{{ route('admin.requests.change-status', $req) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="entered">
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Mark as Entered">
                                        <i class="bi bi-database-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
