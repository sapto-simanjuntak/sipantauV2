<?php

namespace App\Models\Modul;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    public static $taskStatuses = [
        self::STATUS_NOT_STARTED => 'Belum Dimulai',
        self::STATUS_IN_PROGRESS => 'Dalam Proses',
        self::STATUS_COMPLETED => 'Selesai',
    ];

    // Status default untuk tugas baru
    protected $attributes = [
        'status' => self::STATUS_NOT_STARTED,
    ];

    // Kolom-kolom yang bisa diisi
    protected $fillable = [
        'project_id', 'title', 'description', 'status', 'start_date', 'end_date'
    ];

    // Definisikan relasi dengan proyek
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getStatusDescriptionAttribute()
    {
        return self::$taskStatuses[$this->status] ?? 'Status Tidak Diketahui';
    }

    public function getStatusClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_NOT_STARTED:
                return 'badge-not-started';
            case self::STATUS_IN_PROGRESS:
                return 'badge-in-progress';
            case self::STATUS_COMPLETED:
                return 'badge-completed';
            default:
                return 'badge-secondary'; // Kelas default jika status tidak dikenal
        }
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
