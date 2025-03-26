<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Job extends Model
{
   use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'salary_amount',
        'job_sector',
        'job_title',
        'job_position',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
