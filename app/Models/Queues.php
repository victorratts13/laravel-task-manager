<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queues extends Model
{
    use HasFactory;
    protected $table = "queues";
    protected $fillable = ["name", "memory", "status", "limit"];
}
