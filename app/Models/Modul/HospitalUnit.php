<?php

namespace App\Models\Modul;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_code',
        'unit_name',
        'unit_type',
        'location',
        'pic_user_id',
        'is_24_hours',
        'sla_response_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_24_hours' => 'boolean',
        'is_active' => 'boolean',
    ];
}
