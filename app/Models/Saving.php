<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Saving extends Model
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'user_id',
        'saving_goal',
        'start_date',
        'end_date',
        'saving_amount',
        'note',
        'saving_total',
    ];
}
