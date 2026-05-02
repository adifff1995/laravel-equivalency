<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStatusHistory extends Model
{
    protected $table = 'request_status_histories';

    protected $fillable = [
        'request_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
    ];

    public function request()
    {
        return $this->belongsTo(EquivalencyRequest::class, 'request_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
