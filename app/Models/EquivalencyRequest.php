<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EquivalencyRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'tracking_code',
        'name',
        'student_id',
        'email',
        'phone',
        'type',
        'major',
        'old_student_id',
        'new_student_id',
        'courses',
        'university',
        'attachments',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * Generate a unique, human-readable tracking code.
     * Format: EQ-XXXXXXXX  (e.g. EQ-A3F92B17)
     */
    public static function generateTrackingCode(): string
    {
        do {
            $code = 'EQ-' . strtoupper(Str::random(8));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    protected $casts = [
        'attachments' => 'array',
    ];

    // ── Status constants ──────────────────────────────────────────────────────

    const STATUS_NEW             = 'new';
    const STATUS_UNDER_REVIEW    = 'under_review';
    const STATUS_READY_FOR_ENTRY = 'ready_for_entry';
    const STATUS_ENTERED         = 'entered';
    const STATUS_APPROVED        = 'approved';
    const STATUS_REJECTED        = 'rejected';

    const TYPE_SPECIAL         = 'special';
    const TYPE_INTERNAL        = 'internal';
    const TYPE_EXTERNAL_BRIDGE = 'external_bridge';
    const TYPE_EXTERNAL_OTHER  = 'external_other';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_NEW             => 'New',
            self::STATUS_UNDER_REVIEW    => 'Under Review',
            self::STATUS_READY_FOR_ENTRY => 'Ready for Entry',
            self::STATUS_ENTERED         => 'Entered',
            self::STATUS_APPROVED        => 'Approved',
            self::STATUS_REJECTED        => 'Rejected',
        ];
    }

    public static function typeLabels(): array
    {
        return [
            self::TYPE_SPECIAL         => 'Special',
            self::TYPE_INTERNAL        => 'Internal Transfer',
            self::TYPE_EXTERNAL_BRIDGE => 'External (Bridge)',
            self::TYPE_EXTERNAL_OTHER  => 'External (Other)',
        ];
    }

    public static function statusBadgeClass(): array
    {
        return [
            self::STATUS_NEW             => 'badge-status-new',
            self::STATUS_UNDER_REVIEW    => 'badge-status-review',
            self::STATUS_READY_FOR_ENTRY => 'badge-status-ready',
            self::STATUS_ENTERED         => 'badge-status-entered',
            self::STATUS_APPROVED        => 'badge-status-approved',
            self::STATUS_REJECTED        => 'badge-status-rejected',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusHistories()
    {
        return $this->hasMany(RequestStatusHistory::class, 'request_id')->latest();
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::statusBadgeClass()[$this->status] ?? 'bg-secondary';
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByTrackingCode($query, string $code)
    {
        return $query->where('tracking_code', strtoupper(trim($code)));
    }

    public function scopeFilterType($query, ?string $type)
    {
        if ($type) {
            $query->where('type', $type);
        }
        return $query;
    }
}
