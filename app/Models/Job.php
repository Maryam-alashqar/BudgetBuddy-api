<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Job extends Model
{
   use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'salary_amount',
        'payday',
        'job_sector',
        'job_title',
        'job_position',
        'created_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
{
    static::creating(function ($user) {
        // If payday is not already set, set it to the first day of the current month
        if (!$user->payday) {
            $user->payday = Carbon::now()->startOfMonth(); // Sets the payday to the 1st of the current month
        }
    });
}

}
