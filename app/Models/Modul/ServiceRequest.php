<?php

namespace App\Models\Modul;

use App\Models\User;
use App\Models\Modul\HospitalUnit;
use App\Models\Master\ProblemCategory;
use App\Models\Modul\ServiceRequestLog;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ProblemSubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'requester_name',
        'requester_phone',
        'unit_id',
        'issue_title',
        'description',
        'problem_category_id',
        'problem_sub_category_id',
        'severity_level',
        'priority',
        'impact_patient_care',
        'location',
        'device_affected',
        'ip_address',
        'connection_status',
        'occurrence_time',
        'sla_deadline',
        'expected_action',
        'file_path',
        'validation_status',
        'validation_notes',
        'validated_at',
        'validated_by',
        'assigned_to',
        'assigned_at',
        'assigned_by',
        'closed_at',
        'closed_by',
        'closure_notes',
        'user_satisfaction',
        'ticket_status',
    ];

    protected $casts = [
        'impact_patient_care' => 'boolean',
        'occurrence_time' => 'datetime',
        'sla_deadline' => 'datetime',
        'validated_at' => 'datetime',
        'assigned_at' => 'datetime',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the user (requester) that owns the ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the hospital unit
     */
    public function hospitalUnit()
    {
        return $this->belongsTo(HospitalUnit::class, 'unit_id');
    }

    /**
     * Get the problem category
     */
    public function problemCategory()
    {
        return $this->belongsTo(ProblemCategory::class, 'problem_category_id');
    }

    /**
     * Get the problem sub-category
     */
    public function problemSubCategory()
    {
        return $this->belongsTo(ProblemSubCategory::class, 'problem_sub_category_id');
    }

    /**
     * Get the assigned technician
     */
    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * ✅ Get the validator (who approved/rejected)
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * ✅ Get the user who assigned the ticket
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * ✅ Get the user who closed the ticket
     */
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * ✅ Get all activity logs for this ticket
     */
    public function logs()
    {
        return $this->hasMany(ServiceRequestLog::class, 'service_request_id');
    }

    // ========================================
    // BOOT METHOD
    // ========================================

    protected static function boot()
    {
        parent::boot();

        // Auto-generate ticket number on creating
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber()
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');

        // Generate random alphanumeric 6 chars
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

        return $prefix . '-' . $date . '-' . $random;
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('ticket_status', $status);
    }

    /**
     * Scope untuk filter berdasarkan priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope untuk tiket yang assigned ke user tertentu
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope untuk tiket yang dibuat oleh user tertentu
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if ticket is overdue SLA
     */
    public function isOverdue()
    {
        if (!$this->sla_deadline) {
            return false;
        }

        return now()->isAfter($this->sla_deadline);
    }

    /**
     * Get hours remaining until SLA deadline
     */
    public function hoursRemaining()
    {
        if (!$this->sla_deadline) {
            return null;
        }

        return now()->diffInHours($this->sla_deadline, false);
    }
}
