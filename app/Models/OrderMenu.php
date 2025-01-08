<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class OrderMenu extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'menu_id',
        'order_id',
        'count',
    ];
    protected $casts = [
        'uuid' => 'string',
        'menu_id'=>'integer',
        'order_id'=>'integer',
        'count'=>'integer',
    ];
  
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
   
}
