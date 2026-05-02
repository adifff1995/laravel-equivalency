@extends('layouts.app')

@section('title', 'Submit Course Equivalency Request')

@section('content')
<div style="background: linear-gradient(135deg, #1a3c5e 0%, #2c6fad 100%); min-height: 100vh; padding: 2rem 1rem;">

    {{-- Header --}}
    <div class="text-center text-white mb-4">
        <i class="bi bi-mortarboard-fill" style="font-size:2.5rem;color:#e8a820"></i>
        <h3 class="mt-2 fw-700">Course Equivalency Request</h3>
        <p class="opacity-75">IT Faculty — An-Najah National University</p>
        <a href="{{ route('requests.track') }}" class="btn btn-sm btn-outline-light mt-1">
            <i class="bi bi-search me-1"></i> Track an existing request
        </a>
    </div>

    <div class="card mx-auto" style="max-width:780px;border-radius:14px;overflow:hidden">
        <div class="card-header" style="background:#1a3c5e;color:#fff;padding:1.2rem 1.5rem">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-plus fs-5" style="color:#e8a820"></i>
                <span class="fw-600">New Equivalency Request</span>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="alert alert-info d-flex gap-2 py-2 mb-4" style="font-size:0.85rem">
                <i class="bi bi-info-circle-fill mt-1 flex-shrink-0" style="color:#0d6efd"></i>
                <div>No account needed. After submission you will receive a <strong>unique tracking code</strong> — save it to follow up on your request.</div>
            </div>

            <form method="POST" action="{{ route('requests.public.store') }}" enctype="multipart/form-data" id="requestForm">
                @csrf

                {{-- ── Student Information ── --}}
                <h6 class="section-label">
                    <i class="bi bi-person me-1"></i> Student Information
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-500">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Ahmed Al-Rashidi" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Student ID <span class="text-danger">*</span></label>
                        <input type="text" name="student_id" value="{{ old('student_id') }}"
                               class="form-control @error('student_id') is-invalid @enderror"
                               placeholder="e.g. 2021001" required>
                        @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Email <span class="text-muted fw-normal">(optional — for follow-up)</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="student@example.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Phone <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="+970 5x xxx xxxx">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Major / Specialization <span class="text-danger">*</span></label>
                        <input type="text" name="major" value="{{ old('major') }}"
                               class="form-control @error('major') is-invalid @enderror"
                               placeholder="e.g. Computer Science" required>
                        @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Request Type <span class="text-danger">*</span></label>
                        <select name="type" id="typeSelect"
                                class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">— Select type —</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- ── Dynamic: Internal Transfer ── --}}
                <div id="internalFields" class="mb-4 p-3 rounded" style="background:#f0f7ff;display:none">
                    <h6 class="fw-600 mb-3" style="color:#1a3c5e;font-size:0.85rem">
                        <i class="bi bi-arrow-left-right me-1"></i> Transfer Details
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Previous Student ID</label>
                            <input type="text" name="old_student_id" value="{{ old('old_student_id') }}"
                                   class="form-control @error('old_student_id') is-invalid @enderror"
                                   placeholder="Old university ID">
                            @error('old_student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">New Student ID</label>
                            <input type="text" name="new_student_id" value="{{ old('new_student_id') }}"
                                   class="form-control @error('new_student_id') is-invalid @enderror"
                                   placeholder="New university ID">
                            @error('new_student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- ── Dynamic: External University ── --}}
                <div id="universityField" class="mb-4" style="display:none">
                    <label class="form-label fw-500">Previous University <span class="text-danger">*</span></label>
                    <input type="text" name="university" value="{{ old('university') }}"
                           class="form-control @error('university') is-invalid @enderror"
                           placeholder="e.g. Birzeit University">
                    @error('university') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ── Courses ── --}}
                <h6 class="section-label"><i class="bi bi-list-check me-1"></i> Courses for Equivalency</h6>

                <div class="mb-4">
                    <label class="form-label fw-500">Course List <span class="text-danger">*</span></label>
                    <textarea name="courses" rows="5"
                              class="form-control @error('courses') is-invalid @enderror"
                              placeholder="List each course on a new line, e.g.:&#10;CS101 - Introduction to Programming&#10;MATH201 - Calculus I" required>{{ old('courses') }}</textarea>
                    <div class="form-text">List each course on a separate line with its code and name.</div>
                    @error('courses') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ── Attachments ── --}}
                <h6 class="section-label"><i class="bi bi-paperclip me-1"></i> Attachments</h6>

                <div class="mb-4">
                    <label class="form-label fw-500">Upload Documents</label>
                    <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                           class="form-control @error('attachments') is-invalid @enderror">
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        Accepted: PDF, JPG, PNG, DOC, DOCX — Max 5 MB per file.
                    </div>
                    @error('attachments.*') <div class="text-danger" style="font-size:0.85rem">{{ $message }}</div> @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-500">
                        <i class="bi bi-send me-2"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-white-50" style="font-size:0.8rem">
            Staff login →
        </a>
    </div>
</div>

<style>
.section-label {
    font-weight: 700;
    margin-bottom: .75rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
    color: #1a3c5e;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: .08em;
}
</style>
@endsection

@push('scripts')
<script>
    const typeSelect = document.getElementById('typeSelect');
    const internalFields = document.getElementById('internalFields');
    const universityField = document.getElementById('universityField');

    function updateFields() {
        const type = typeSelect.value;
        internalFields.style.display  = (type === 'internal') ? '' : 'none';
        universityField.style.display = (type === 'external_bridge' || type === 'external_other') ? '' : 'none';
    }

    typeSelect.addEventListener('change', updateFields);
    updateFields();
</script>
@endpush
