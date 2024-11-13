<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'background_jobs';

    protected $fillable = [
        'class_name', 'method', 'parameters', 'retries', 'retry_count', 'priority', 'status'
    ];
}
