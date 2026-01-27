<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_category_id',
        'sub_category_name',
    ];

    public function category()
    {
        return $this->belongsTo(ProblemCategory::class, 'problem_category_id');
    }
}
