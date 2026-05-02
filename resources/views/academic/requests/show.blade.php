@extends('layouts.app')

@section('title', 'Review Request #' . $request->id)
@section('page-title', 'Review Request')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('academic.requests.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
    <span class="badge {{ $request->status_badge_class }} py-2 px-3 fs-6">
        {{ $request->status_label }}
    </span>
</div>

<div class="row g-4">
    <div class="col-lg-8">

        {{-- Student Info --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person me-2"></i>Student Information</div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach([
                        ['Student Name',  $request->name],
                        ['Student ID',    $request->student_id],
                        ['Major',         $request->major],
                        ['Request Type',  $request->type_label],
                        ['University',    $request->university],
                        ['Prev. Stud. ID',$request->old_student_id],
                        ['New Stud. ID',  $request->new_student_id],
                    ] as [$label, $value])
                        @if($value)
                        <div class="col-md-6">
                            <div class="text-muted small fw-500 text-uppercase" style="font-size:0.7rem;letter-spacing:0.07em">{{ $label }}</div>
                            <div class="fw-500 mt-1">{{ $value }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Courses --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-list-check me-2"></i>Courses for Equivalency</div>
            <div class="card-body">
                <pre class="mb-0" style="white-space:pre-wrap;font-family:inherit;font-size:0.9rem;color:#334155">{{ $request->courses }}</pre>
            </div>
        </div>

        {{-- Attachments --}}
        @if($request->attachments && count($request->attachments))
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-paperclip me-2"></i>Supporting Documents</div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($request->attachments as $file)
                    <a href="{{ asset('storage/' . $file['path']) }}" target="_blank"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 px-0">
                        <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                        <div>
                            <div class="fw-500 small">{{ $file['original_name'] }}</div>
                            <div class="text-muted" style="font-size:0.75rem">Click to open</div>
                        </div>
                        <i class="bi bi-box-arrow-up-right ms-auto text-muted small"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($request->notes)
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-chat-text me-2"></i>Previous Notes</div>
            <div class="card-body">
                <p class="mb-0" style="white-space:pre-line">{{ $request->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    <div class="col-lg-4">

        {{-- Decision Panel --}}
        @if($request->status === 'entered')
        <div class="card mb-4 border-0" style="box-shadow:0 0 0 2px #6f42c1">
            <div class="card-header text-white" style="background:#6f42c1">
                <i class="bi bi-gavel me-2"></i>Your Decision
            </div>
            <div class="card-body">
                {{-- Approve --}}
                <form method="POST" action="{{ route('academic.requests.decide', $request) }}" class="mb-3">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <div class="mb-2">
                        <label class="form-label small fw-500">Approval Notes (optional)</label>
                        <textarea name="notes" rows="2" class="form-control form-control-sm"
                                  placeholder="Any notes for approval…"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-500"
                            onclick="return confirm('Approve this equivalency request?')">
                        <i class="bi bi-check-circle me-2"></i>Approve Request
                    </button>
                </form>

                <hr>

                {{-- Reject --}}
                <form method="POST" action="{{ route('academic.requests.decide', $request) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <div class="mb-2">
                        <label class="form-label small fw-500">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="notes" rows="3" class="form-control form-control-sm"
                                  placeholder="Provide reason for rejection…" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 fw-500"
                            onclick="return confirm('Reject this request? This cannot be undone.')">
                        <i class="bi bi-x-circle me-2"></i>Reject Request
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Meta --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Request Info</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Submitted By</div>
                    <div class="fw-500">{{ $request->creator->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Date</div>
                    <div class="fw-500">{{ $request->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>

        {{-- History --}}
        @if($request->statusHistories->count())
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Status History</div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($request->statusHistories as $history)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="small">
                            <span class="badge {{ \App\Models\EquivalencyRequest::statusBadgeClass()[$history->new_status] ?? 'bg-secondary' }} mb-1">
                                {{ \App\Models\EquivalencyRequest::statusLabels()[$history->new_status] ?? $history->new_status }}
                            </span>
                            <div class="text-muted" style="font-size:0.75rem">
                                by {{ $history->changedBy->name }}<br>
                                {{ $history->created_at->format('d M Y, h:i A') }}
                            </div>
                            @if($history->notes)
                            <div class="mt-1 p-2 rounded" style="background:#f8fafc;font-size:0.8rem">
                                {{ $history->notes }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
