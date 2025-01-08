<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'uuid',
        'name',      
        'description',       
        'category_id', 
    ];

    protected $casts = [
        'uuid'      => 'string', 
        'name'      => 'string',
        'description'      => 'string',
        'category_id'=> 'integer',
    ];
    public function category():object{
        return $this->belongsTo(Category::class);
    }
 
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
