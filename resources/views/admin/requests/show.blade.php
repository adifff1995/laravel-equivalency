@extends('layouts.app')

@section('title', 'Request #' . $request->id)
@section('page-title', 'Request Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.requests.index') }}" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <a href="{{ route('admin.requests.edit', $request) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
    <span class="badge {{ $request->status_badge_class }} py-2 px-3 fs-6">
        {{ $request->status_label }}
    </span>
</div>

<div class="row g-4">

    {{-- ── Left column: Main details ── --}}
    <div class="col-lg-8">

        {{-- Student Info --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person me-2"></i>Student Information</div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $fields = [
                            ['Student Name',  $request->name],
                            ['Student ID',    $request->student_id],
                            ['Major',         $request->major],
                            ['Request Type',  $request->type_label],
                        ];
                        if ($request->old_student_id) $fields[] = ['Previous Student ID', $request->old_student_id];
                        if ($request->new_student_id) $fields[] = ['New Student ID', $request->new_student_id];
                        if ($request->university)     $fields[] = ['Previous University', $request->university];
                    @endphp
                    @foreach($fields as [$label, $value])
                    <div class="col-md-6">
                        <div class="text-muted small fw-500 text-uppercase" style="font-size:0.7rem;letter-spacing:0.07em">{{ $label }}</div>
                        <div class="fw-500 mt-1">{{ $value ?? '—' }}</div>
                    </div>
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
            <div class="card-header"><i class="bi bi-paperclip me-2"></i>Attachments</div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($request->attachments as $file)
                    <a href="{{ asset('storage/' . $file['path']) }}" target="_blank"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 px-0">
                        @php
                            $ext = pathinfo($file['original_name'], PATHINFO_EXTENSION);
                            $icon = in_array($ext, ['jpg','jpeg','png']) ? 'bi-image' : 'bi-file-earmark-pdf';
                        @endphp
                        <i class="bi {{ $icon }} text-danger fs-5"></i>
                        <div>
                            <div class="fw-500 small">{{ $file['original_name'] }}</div>
                            <div class="text-muted" style="font-size:0.75rem">{{ strtoupper($ext) }}</div>
                        </div>
                        <i class="bi bi-download ms-auto text-muted"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($request->notes)
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-chat-text me-2"></i>Notes</div>
            <div class="card-body">
                <p class="mb-0" style="white-space:pre-line">{{ $request->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    {{-- ── Right column: Status & History ── --}}
    <div class="col-lg-4">

        {{-- Change Status --}}
        @if(in_array($request->status, ['new', 'under_review', 'ready_for_entry']))
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-arrow-right-circle me-2"></i>Change Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.requests.change-status', $request) }}">
                    @csrf @method('PATCH')

                    @if($request->status === 'new')
                        <p class="text-muted small mb-3">Move to <strong>Under Review</strong> to begin processing.</p>
                        <input type="hidden" name="status" value="under_review">
                        <div class="mb-3">
                            <label class="form-label small fw-500">Notes (optional)</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm"
                                      placeholder="Add notes…"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-right-circle me-2"></i>Mark Under Review
                        </button>

                    @elseif($request->status === 'under_review')
                        <p class="text-muted small mb-3">Mark as <strong>Ready for Entry</strong> once review is complete.</p>
                        <input type="hidden" name="status" value="ready_for_entry">
                        <div class="mb-3">
                            <label class="form-label small fw-500">Notes (optional)</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm"
                                      placeholder="Add notes…"></textarea>
                        </div>
                        <button type="submit" class="btn w-100 text-white" style="background:#fd7e14">
                            <i class="bi bi-check-circle me-2"></i>Mark Ready for Entry
                        </button>

                    @elseif($request->status === 'ready_for_entry')
                        <p class="text-muted small mb-3">Confirm data has been <strong>Entered</strong> into the system.</p>
                        <input type="hidden" name="status" value="entered">
                        <div class="mb-3">
                            <label class="form-label small fw-500">Notes (optional)</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm"
                                      placeholder="Add notes…"></textarea>
                        </div>
                        <button type="submit" class="btn w-100 text-white" style="background:#6f42c1">
                            <i class="bi bi-database-check me-2"></i>Mark as Entered
                        </button>
                    @endif
                </form>
            </div>
        </div>
        @endif

        {{-- Meta --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Request Info</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Tracking Code</div>
                    <div class="mono fw-700" style="color:#1a3c5e;font-size:1rem;letter-spacing:.06em">
                        {{ $request->tracking_code }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Created By</div>
                    <div class="fw-500">{{ $request->creator?->name ?? 'Public Submission' }}</div>
                </div>
                @if($request->email)
                <div class="mb-3">
                    <div class="text-muted small">Student Email</div>
                    <div class="fw-500">{{ $request->email }}</div>
                </div>
                @endif
                @if($request->phone)
                <div class="mb-3">
                    <div class="text-muted small">Phone</div>
                    <div class="fw-500">{{ $request->phone }}</div>
                </div>
                @endif
                <div class="mb-3">
                    <div class="text-muted small">Submitted</div>
                    <div class="fw-500">{{ $request->created_at->format('d M Y, h:i A') }}</div>
                </div>
                <div>
                    <div class="text-muted small">Last Updated</div>
                    <div class="fw-500">{{ $request->updated_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>

        {{-- Status History --}}
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
