<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bonuses extends Model
{
use HasFactory;

    protected $fillable = [
        'user_id',
        'receives_bonus',
        'bonuses_amount',
        'bonus_date',
        'is_permanent'];

    public function user()
    {
        return $this->belongsTo(User::class)->where('role', 'fixed_income');
    }
}
