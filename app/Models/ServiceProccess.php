<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProccess extends Model
{
    use HasFactory;
    protected $table = "service_proccesses";
    protected $fillable = ["env", "status", "pid", "tag", "uuid", "command", "loggable", "interval", "last_execution"];

    public function enviroment() {
        return $this->hasOne(Enviromet::class, 'id', 'env');
    }

    public function logs() {
        return $this->hasMany(ServiceLogs::class, 'service', 'id');
    }
}
