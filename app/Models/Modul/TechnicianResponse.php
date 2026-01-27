<?php

namespace App\Models\Modul;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TechnicianResponse extends Model
{
    protected $table = 'technician_responses';
    protected $fillable = [
        'service_request_id',
        'ticket_number',
        'technician_id',
        'assigned_by',
        'status',
        'diagnosis',
        'action_taken',
        'technician_notes',
        'assigned_at',
        'started_at',
        'resolved_at',
        'completion_time',
        'attachment_path',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'completion_time' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    // Get technician info from passport DB
    public function getTechnicianAttribute()
    {
        return DB::connection('mysql_passport')
            ->table('users')
            ->where('id', $this->technician_id)
            ->select('id', 'first_name', 'last_name', 'email')
            ->first();
    }

    // Get admin who assigned
    public function getAssignerAttribute()
    {
        return DB::connection('mysql_passport')
            ->table('users')
            ->where('id', $this->assigned_by)
            ->select('id', 'first_name', 'last_name')
            ->first();
    }
}
