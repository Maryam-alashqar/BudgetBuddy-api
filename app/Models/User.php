<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'salary_type',
        'otp',
        'otp_expires_at',
        'device_token'
    ];
    // Accessor: Convert boolean to 'fixed' or 'irregular' when retrieved
    public function getSalaryTypeAttribute($value)
    {
        return $value ? 'fixed' : 'irregular';
    }

    // Mutator: Convert 'fixed' or 'irregular' to boolean when stored
    public function setSalaryTypeAttribute($value)
    {
        $this->attributes['salary_type'] = ($value === 'fixed') ? 1 : 0;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     public function jobs()
    {
        return $this->hasMany(Job::class);
    }
      public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
