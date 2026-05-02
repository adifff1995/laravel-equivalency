@extends('layouts.app')

@section('title', 'Pending Approval')
@section('page-title', 'Requests Pending Approval')

@section('content')

<div class="alert alert-info d-flex align-items-center gap-2 mb-4" style="font-size:0.88rem">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div>Showing only requests with status <strong>Entered</strong> that are ready for your approval.</div>
</div>

{{-- Search --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('academic.requests.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
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
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('academic.requests.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-clipboard-check me-2"></i>Requests Ready for Decision
            <span class="badge bg-purple ms-2" style="background:#6f42c1!important">{{ $requests->total() }}</span>
        </span>
    </div>

    @if($requests->isEmpty())
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-check2-all fs-1 d-block mb-3 text-success opacity-50"></i>
            <strong>All caught up!</strong><br>
            No requests are currently awaiting your approval.
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Major</th>
                    <th>University</th>
                    <th>Submitted</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                <tr>
                    <td class="text-muted mono" style="font-size:0.8rem">{{ $req->id }}</td>
                    <td class="fw-500">{{ $req->name }}</td>
                    <td class="mono">{{ $req->student_id }}</td>
                    <td>
                        <span class="badge bg-light text-dark border" style="font-weight:500">
                            {{ $req->type_label }}
                        </span>
                    </td>
                    <td>{{ $req->major }}</td>
                    <td class="text-muted small">{{ $req->university ?? '—' }}</td>
                    <td class="text-muted small">{{ $req->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('academic.requests.show', $req) }}"
                               class="btn btn-sm btn-outline-secondary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            {{-- Quick Approve --}}
                            <form method="POST" action="{{ route('academic.requests.decide', $req) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-success" title="Approve"
                                        onclick="return confirm('Approve this request?')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            {{-- Quick Reject --}}
                            <button type="button" class="btn btn-sm btn-danger" title="Reject"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        {{-- Reject Modal --}}
                        <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Request #{{ $req->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('academic.requests.decide', $req) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <div class="modal-body">
                                            <p class="text-muted small">Rejecting request for <strong>{{ $req->name }}</strong> ({{ $req->student_id }})</p>
                                            <label class="form-label fw-500">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea name="notes" rows="3" class="form-control"
                                                      placeholder="Provide a reason for rejection…" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
