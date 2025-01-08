<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Restaurant extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'uuid',
        'name',
        'cuisine_type',
        'location',
        'phone',
    ];

    protected $casts = [
        'uuid'    => 'string',
        'phone' => 'string',
        'name'    => 'string' ,
        'cuisine_type' => 'string',
        'location' => 'string',

    ];
    protected $appends = ['average_rate']; 
   
    public function ratings(){
        return $this->hasMany(Rating::class);
    }
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public function getAverageRateAttribute()
    {
       $star= $this->ratings->avg('star');
       if ($star==0)
      { return $star=0;}
      else
      { return $star;}
    }
}
