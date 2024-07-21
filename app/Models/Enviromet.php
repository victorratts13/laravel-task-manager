<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enviromet extends Model
{
    use HasFactory;
    protected $table = "enviromets";
    protected $fillable = ['user', 'path', 'name', 'status', 'queue', 'variables'];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user');
    }

    public function queue() {
        return $this->hasOne(Queues::class, 'id', 'queue');
    }

    public function services() {
        return $this->hasMany(ServiceProccess::class, 'env', 'id');
    }
}
