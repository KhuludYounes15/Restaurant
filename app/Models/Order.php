<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'user_id',
        'restaurant_id',
        'status', 
    ];

    protected $casts = [
        'uuid' => 'string',
        'restaurant_id'=>'integer',
        'user_id'=>'integer',
    ];
    protected $appends=['total_price'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orderMenus()
    {
        return $this->hasMany(OrderMenu::class);
    }
    public function getTotalPriceAttribute()
    {
        $totalPrice = 0;
    
        foreach ($this->orderMenus as $orderMenu) {
            $totalPrice += $orderMenu->menu->price * $orderMenu->count;
        }
    
        return $totalPrice;
    }
}
