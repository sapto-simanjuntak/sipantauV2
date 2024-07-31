<?php

namespace App\Models\Modul;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    const STATUS_NOT_STARTED = 'Not Started';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_CANCELLED = 'Cancelled';

    public static $statuses = [
        self::STATUS_NOT_STARTED => 'Belum Dimulai',
        self::STATUS_IN_PROGRESS => 'Dalam Proses',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';

    public static $validated = [
        self::STATUS_PENDING => 'Menunggu Validasi',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
    ];


    // Status default untuk proyek baru
    protected $attributes = [
        'status' => self::STATUS_NOT_STARTED,
        'validated' => self::STATUS_PENDING, // Tambahkan status_validasi default
    ];

    // Kolom-kolom yang bisa diisi
    protected $fillable = [
        'name', 'description', 'start_date', 'end_date', 'status'
    ];

    // Definisikan relasi dengan tugas
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }



    // public function projects()
    // {
    //     return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    // }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }


    public function pics()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }


    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_user');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
