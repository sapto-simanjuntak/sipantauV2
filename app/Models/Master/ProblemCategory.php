<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'category_code',
        'requires_validation',
        'default_sla_hours',
    ];

    protected $casts = [
        'requires_validation' => 'boolean',
    ];

    public function subCategories()
    {
        return $this->hasMany(ProblemSubCategory::class);
    }
}
