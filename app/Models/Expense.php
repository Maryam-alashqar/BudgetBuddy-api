<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    protected $fillable = [
        'user_id',
        'category',
        'subcategory',
        'is_custom_subcategory',
        'expenses_name',
        'expenses_amount',
        'expenses_total'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
