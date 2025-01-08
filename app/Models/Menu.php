<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Menu extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'uuid',
        'restaurant_id',
        'product_id',
        'price',
    
    ];
 
    
    protected $casts = [
        'uuid' => 'string',
        'price' => 'double',
        'product_id'=>'integer',
        'restaurant_id'=>'integer',
    ];
    public function product():object
    {
        return $this->belongsTo(Product::class);
    }
    public function restaurant():object
    {
        return $this->belongsTo(Restaurant::class);
    }
    public function orderMenus()
    {
        return $this->hasMany(OrderMenu::class);
    }
}
