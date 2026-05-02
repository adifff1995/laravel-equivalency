<?php

namespace App\Services;

use App\Models\EquivalencyRequest;
use App\Models\RequestStatusHistory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequestService
{
    /**
     * Create a new equivalency request from public form (no auth required).
     */
    public function createFromPublicForm(array $data, array $files = []): EquivalencyRequest
    {
        $attachments = $this->handleFileUploads($files);

        return EquivalencyRequest::create([
            'tracking_code' => EquivalencyRequest::generateTrackingCode(),
            'name'          => $data['name'],
            'student_id'    => $data['student_id'],
            'email'         => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'type'          => $data['type'],
            'major'         => $data['major'],
            'old_student_id'=> $data['old_student_id'] ?? null,
            'new_student_id'=> $data['new_student_id'] ?? null,
            'courses'       => $data['courses'],
            'university'    => $data['university'] ?? null,
            'attachments'   => $attachments,
            'status'        => EquivalencyRequest::STATUS_NEW,
            'created_by'    => Auth::id(), // null for public submissions — column is now nullable
        ]);
    }

    /**
     * Update an existing request (admin only).
     */
    public function update(EquivalencyRequest $request, array $data, array $files = []): EquivalencyRequest
    {
        $attachments = $request->attachments ?? [];

        if (!empty($files['attachments'])) {
            $newFiles    = $this->handleFileUploads($files);
            $attachments = array_merge($attachments, $newFiles);
        }

        $request->update([
            'name'          => $data['name'],
            'student_id'    => $data['student_id'],
            'type'          => $data['type'],
            'major'         => $data['major'],
            'old_student_id'=> $data['old_student_id'] ?? null,
            'new_student_id'=> $data['new_student_id'] ?? null,
            'courses'       => $data['courses'],
            'university'    => $data['university'] ?? null,
            'attachments'   => $attachments,
        ]);

        return $request->fresh();
    }

    /**
     * Change status and log to history.
     */
    public function changeStatus(
        EquivalencyRequest $request,
        string $newStatus,
        ?string $notes = null
    ): EquivalencyRequest {
        $oldStatus = $request->status;

        $request->update([
            'status' => $newStatus,
            'notes'  => $notes ?? $request->notes,
        ]);

        RequestStatusHistory::create([
            'request_id' => $request->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes'      => $notes,
            'changed_by' => Auth::id(),
        ]);

        return $request->fresh();
    }

    /**
     * Handle file uploads, return array of stored paths.
     */
    private function handleFileUploads(array $files): array
    {
        $paths = [];

        if (!empty($files['attachments'])) {
            $uploads = is_array($files['attachments'])
                ? $files['attachments']
                : [$files['attachments']];

            foreach ($uploads as $file) {
                if ($file instanceof UploadedFile) {
                    $path    = $file->store('attachments', 'public');
                    $paths[] = [
                        'path'         => $path,
                        'original_name'=> $file->getClientOriginalName(),
                        'mime'         => $file->getMimeType(),
                    ];
                }
            }
        }

        return $paths;
    }
}
