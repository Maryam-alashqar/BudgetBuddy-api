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
        'saving_type',
        'saving_name',
        'saving_amount',
        'saving_total',
        'created_at'
    ];
}
