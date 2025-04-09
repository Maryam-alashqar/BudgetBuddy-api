<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Budget extends Model
{
     use HasFactory, Notifiable;

    protected $table = 'budget';

   protected $fillable = [
        'user_id',
        'needs_percentage',
        'wants_percentage',
        'savings_percentage',
         'needs_amount',
        'wants_amount',
        'savings_amount'
       ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
