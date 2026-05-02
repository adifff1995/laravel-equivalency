{{--
    Shared form fields for create & edit views.
    Variables expected: $types (array), $request (optional, for old values)
--}}

@php $r = $request ?? null; @endphp

<h6 class="fw-700 mb-3 pb-2 border-bottom" style="color:#1a3c5e;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.08em">
    <i class="bi bi-person me-1"></i> Student Information
</h6>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-500">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name"
               value="{{ old('name', $r?->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-500">Student ID <span class="text-danger">*</span></label>
        <input type="text" name="student_id"
               value="{{ old('student_id', $r?->student_id) }}"
               class="form-control @error('student_id') is-invalid @enderror" required>
        @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-500">Major <span class="text-danger">*</span></label>
        <input type="text" name="major"
               value="{{ old('major', $r?->major) }}"
               class="form-control @error('major') is-invalid @enderror" required>
        @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-500">Request Type <span class="text-danger">*</span></label>
        <select name="type" id="typeSelect"
                class="form-select @error('type') is-invalid @enderror" required>
            <option value="">— Select type —</option>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" {{ old('type', $r?->type) === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

{{-- Internal --}}
<div id="internalFields" style="display:none" class="mb-4 p-3 rounded bg-light">
    <h6 class="fw-600 mb-3 small">Transfer Details</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-500">Previous Student ID</label>
            <input type="text" name="old_student_id"
                   value="{{ old('old_student_id', $r?->old_student_id) }}"
                   class="form-control @error('old_student_id') is-invalid @enderror">
            @error('old_student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-500">New Student ID</label>
            <input type="text" name="new_student_id"
                   value="{{ old('new_student_id', $r?->new_student_id) }}"
                   class="form-control @error('new_student_id') is-invalid @enderror">
            @error('new_student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

{{-- External --}}
<div id="universityField" style="display:none" class="mb-4">
    <label class="form-label fw-500">Previous University <span class="text-danger">*</span></label>
    <input type="text" name="university"
           value="{{ old('university', $r?->university) }}"
           class="form-control @error('university') is-invalid @enderror"
           placeholder="e.g. Birzeit University">
    @error('university') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<h6 class="fw-700 mb-3 pb-2 border-bottom" style="color:#1a3c5e;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.08em">
    <i class="bi bi-list-check me-1"></i> Courses
</h6>

<div class="mb-4">
    <label class="form-label fw-500">Course List <span class="text-danger">*</span></label>
    <textarea name="courses" rows="6"
              class="form-control @error('courses') is-invalid @enderror"
              placeholder="CS101 - Introduction to Programming&#10;MATH201 - Calculus I" required>{{ old('courses', $r?->courses) }}</textarea>
    @error('courses') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<h6 class="fw-700 mb-3 pb-2 border-bottom" style="color:#1a3c5e;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.08em">
    <i class="bi bi-paperclip me-1"></i> Attachments
</h6>

@if($r && $r->attachments && count($r->attachments))
<div class="mb-3">
    <div class="small fw-500 mb-2">Existing Attachments:</div>
    @foreach($r->attachments as $file)
    <div class="d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-file-earmark text-muted"></i>
        <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="small">
            {{ $file['original_name'] }}
        </a>
    </div>
    @endforeach
</div>
@endif

<div class="mb-4">
    <label class="form-label fw-500">Upload New Documents</label>
    <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
           class="form-control @error('attachments') is-invalid @enderror">
    <div class="form-text">New files will be added to existing attachments. Max 5 MB per file.</div>
    @error('attachments.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

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
