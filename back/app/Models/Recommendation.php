<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'cuisine_ids', 'category_ids'];

    protected $casts = [
        'cuisine_ids' => 'array',
        'category_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
