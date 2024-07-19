<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enviromet extends Model
{
    use HasFactory;
    protected $table = "enviromets";
    protected $fillable = ['user', 'path', 'name', 'status'];
}
