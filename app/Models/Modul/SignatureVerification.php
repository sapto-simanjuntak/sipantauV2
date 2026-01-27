<?php

namespace App\Models\Modul;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SignatureVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'verified_role',
        'verification_status',
        'verified_by_ip',
        'verified_by_user_agent',
        'verified_by_user_id',
        'verification_data',
        'failure_reason',
    ];

    protected $casts = [
        'verification_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship ke service request
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'ticket_number', 'ticket_number');
    }

    /**
     * Relationship ke user yang verify (optional)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
