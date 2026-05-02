@extends('layouts.app')

@section('title', 'Request ' . $req->tracking_code)

@section('content')
<div style="background: linear-gradient(135deg, #1a3c5e 0%, #2c6fad 100%); min-height: 100vh; padding: 2rem 1rem;">

    {{-- Header --}}
    <div class="text-center text-white mb-4">
        <i class="bi bi-mortarboard-fill" style="font-size:2rem;color:#e8a820"></i>
        <h4 class="mt-2 fw-700 mb-0">Request Status</h4>
        <p class="opacity-75" style="font-size:.85rem">IT Faculty — Course Equivalency System</p>
    </div>

    <div class="mx-auto" style="max-width:740px">

        {{-- ── Status Hero Card ── --}}
        <div class="card mb-4 border-0 shadow" style="border-radius:14px;overflow:hidden">
            <div class="p-4" style="background:#1a3c5e">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="text-white-50 small mb-1">Tracking Code</div>
                        <div class="mono fw-700 text-white" style="font-size:1.3rem;letter-spacing:.08em">
                            {{ $req->tracking_code }}
                        </div>
                    </div>
                    <span class="badge {{ $req->status_badge_class }} py-2 px-4" style="font-size:.9rem">
                        {{ $req->status_label }}
                    </span>
                </div>
            </div>

            {{-- Progress bar --}}
            @php
                $steps = ['new','under_review','ready_for_entry','entered','approved'];
                $currentIdx = array_search($req->status, $steps);
                $isRejected = $req->status === 'rejected';
                $progress = $isRejected ? 100 : (($currentIdx !== false ? $currentIdx + 1 : 1) / count($steps) * 100);
                $barColor  = $isRejected ? '#dc3545' : ($req->status === 'approved' ? '#198754' : '#2c6fad');
            @endphp
            <div style="height:6px;background:#e2e8f0">
                <div style="height:6px;background:{{ $barColor }};width:{{ $progress }}%;transition:width .5s"></div>
            </div>

            {{-- Step indicators --}}
            <div class="px-4 py-3" style="background:#f8fafc">
                <div class="d-flex justify-content-between" style="font-size:.68rem;color:#64748b;text-transform:uppercase;letter-spacing:.06em">
                    @foreach(['New','Under Review','Ready','Entered','Decided'] as $i => $stepLabel)
                    @php $done = !$isRejected && $currentIdx !== false && $i <= $currentIdx; @endphp
                    <div class="text-center" style="flex:1">
                        <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center"
                             style="width:22px;height:22px;background:{{ $done ? $barColor : '#e2e8f0' }};color:{{ $done ? '#fff' : '#94a3b8' }};font-size:.65rem;font-weight:700">
                            {{ $done ? '✓' : ($i + 1) }}
                        </div>
                        <div style="color:{{ $done ? $barColor : '#94a3b8' }}">{{ $stepLabel }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Approved / Rejected Banner ── --}}
        @if($req->status === 'approved')
        <div class="alert d-flex align-items-center gap-3 mb-4 border-0 shadow-sm" style="background:#d1fae5;border-radius:12px">
            <div style="width:48px;height:48px;background:#059669;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-check-lg text-white" style="font-size:1.4rem"></i>
            </div>
            <div>
                <div class="fw-700" style="color:#065f46">Your request has been Approved!</div>
                <div class="small" style="color:#047857">Please contact the academic affairs office to complete the process.</div>
            </div>
        </div>
        @elseif($req->status === 'rejected')
        <div class="alert d-flex align-items-center gap-3 mb-4 border-0 shadow-sm" style="background:#fee2e2;border-radius:12px">
            <div style="width:48px;height:48px;background:#dc2626;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-x-lg text-white" style="font-size:1.2rem"></i>
            </div>
            <div>
                <div class="fw-700" style="color:#991b1b">Your request has been Rejected</div>
                <div class="small" style="color:#b91c1c">Please review the notes below and contact the faculty if you have questions.</div>
            </div>
        </div>
        @endif

        <div class="row g-4">

            {{-- ── Left: Request Details ── --}}
            <div class="col-lg-7">

                {{-- Student Info --}}
                <div class="card mb-4 border-0 shadow-sm" style="border-radius:12px">
                    <div class="card-header bg-white border-bottom" style="border-radius:12px 12px 0 0">
                        <span class="fw-600" style="color:#1a3c5e"><i class="bi bi-person me-2"></i>Student Information</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @php
                                $fields = [
                                    ['Full Name',   $req->name],
                                    ['Student ID',  $req->student_id],
                                    ['Major',       $req->major],
                                    ['Request Type',$req->type_label],
                                ];
                                if ($req->university)     $fields[] = ['University',    $req->university];
                                if ($req->old_student_id) $fields[] = ['Previous ID',  $req->old_student_id];
                                if ($req->new_student_id) $fields[] = ['New ID',        $req->new_student_id];
                            @endphp
                            @foreach($fields as [$label, $value])
                            <div class="col-6">
                                <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;font-weight:600">{{ $label }}</div>
                                <div class="fw-500 mt-1" style="font-size:.9rem">{{ $value }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Courses --}}
                <div class="card mb-4 border-0 shadow-sm" style="border-radius:12px">
                    <div class="card-header bg-white border-bottom" style="border-radius:12px 12px 0 0">
                        <span class="fw-600" style="color:#1a3c5e"><i class="bi bi-list-check me-2"></i>Courses Submitted</span>
                    </div>
                    <div class="card-body">
                        <pre style="white-space:pre-wrap;font-family:inherit;font-size:.88rem;color:#334155;margin:0">{{ $req->courses }}</pre>
                    </div>
                </div>

                {{-- Attachments --}}
                @if($req->attachments && count($req->attachments))
                <div class="card mb-4 border-0 shadow-sm" style="border-radius:12px">
                    <div class="card-header bg-white border-bottom" style="border-radius:12px 12px 0 0">
                        <span class="fw-600" style="color:#1a3c5e"><i class="bi bi-paperclip me-2"></i>Attached Documents</span>
                    </div>
                    <div class="card-body p-0">
                        @foreach($req->attachments as $file)
                        <a href="{{ asset('storage/' . $file['path']) }}" target="_blank"
                           class="d-flex align-items-center gap-3 px-4 py-3 text-decoration-none border-bottom"
                           style="color:#334155">
                            @php $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION)); @endphp
                            <i class="bi {{ in_array($ext,['jpg','jpeg','png']) ? 'bi-image' : 'bi-file-earmark-pdf' }} text-danger fs-5"></i>
                            <div class="flex-grow-1">
                                <div class="fw-500 small">{{ $file['original_name'] }}</div>
                                <div class="text-muted" style="font-size:.72rem">{{ strtoupper($ext) }} file</div>
                            </div>
                            <i class="bi bi-box-arrow-up-right text-muted small"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Notes (show if any) --}}
                @if($req->notes)
                <div class="card border-0 shadow-sm" style="border-radius:12px;border-left:4px solid #fd7e14!important">
                    <div class="card-body">
                        <div class="fw-600 mb-2" style="color:#1a3c5e;font-size:.85rem">
                            <i class="bi bi-chat-text me-2" style="color:#fd7e14"></i>Notes from Faculty
                        </div>
                        <p class="mb-0" style="white-space:pre-line;font-size:.9rem">{{ $req->notes }}</p>
                    </div>
                </div>
                @endif

            </div>

            {{-- ── Right: Timeline ── --}}
            <div class="col-lg-5">

                {{-- Submission info --}}
                <div class="card mb-4 border-0 shadow-sm" style="border-radius:12px">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Date Submitted</div>
                            <div class="fw-500">{{ $req->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div>
                            <div class="text-muted small">Last Updated</div>
                            <div class="fw-500">{{ $req->updated_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Status Timeline --}}
                <div class="card border-0 shadow-sm" style="border-radius:12px">
                    <div class="card-header bg-white border-bottom" style="border-radius:12px 12px 0 0">
                        <span class="fw-600" style="color:#1a3c5e"><i class="bi bi-clock-history me-2"></i>Status History</span>
                    </div>
                    <div class="card-body">
                        {{-- Initial submission entry --}}
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot" style="background:#6c757d;box-shadow:0 0 0 2px #6c757d"></div>
                                <div class="small">
                                    <span class="badge badge-status-new mb-1">New</span>
                                    <div class="text-muted" style="font-size:.75rem">
                                        Request submitted<br>
                                        {{ $req->created_at->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            </div>

                            @forelse($req->statusHistories->sortBy('created_at') as $history)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="small">
                                    <span class="badge {{ \App\Models\EquivalencyRequest::statusBadgeClass()[$history->new_status] ?? 'bg-secondary' }} mb-1">
                                        {{ \App\Models\EquivalencyRequest::statusLabels()[$history->new_status] ?? $history->new_status }}
                                    </span>
                                    <div class="text-muted" style="font-size:.75rem">
                                        {{ $history->created_at->format('d M Y, h:i A') }}
                                    </div>
                                    @if($history->notes)
                                    <div class="mt-1 p-2 rounded" style="background:#f8fafc;font-size:.8rem;color:#475569">
                                        {{ $history->notes }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="text-muted small">No status updates yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Bottom actions --}}
        <div class="text-center mt-4 d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('requests.track') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-2"></i>Track Another Request
            </a>
            <a href="{{ route('requests.public.create') }}" class="btn btn-outline-light">
                <i class="bi bi-plus me-2"></i>Submit New Request
            </a>
        </div>
    </div>
</div>
