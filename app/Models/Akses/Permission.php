<?php

namespace App\Models\Akses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Spatie\Permission\Models\Permission as Spatiepermission;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';
}
