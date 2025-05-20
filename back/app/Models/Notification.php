<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 
        'triggered_by', 
        'type', 
        'recipe_id', 
        'message', 
        'read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function triggeredBy()
{
    return $this->belongsTo(User::class, 'triggered_by');
}

public function recipe()
{
    return $this->belongsTo(Recipe::class);
}
}