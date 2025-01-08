<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'star',
        'comment',
        'restaurant_id',
        'user_id',
    ];

    protected $casts = [
        'uuid' => 'string',
        'star' => 'integer',
        'comment' => 'string',
    ];
    public function user():object
    {
        return $this->belongsTo(User::class);
    }
    public function restaurant():object
    {
        return $this->belongsTo(Restaurant::class);
    }
}
