<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResett extends Model
{
    use HasFactory;
    


    // Specify the table name
    protected $table = 'password_resets';

    // Optionally, specify which attributes are mass assignable
    protected $fillable = ['email', 'token'];
}

