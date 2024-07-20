<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLogs extends Model
{
    use HasFactory;
    protected $table = "service_logs";
    protected $fillable = ['service', 'command', 'output'];

    public function service() {
        return $this->hasOne(ServiceProccess::class, 'id', 'service');
    }
}
