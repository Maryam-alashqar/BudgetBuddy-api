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
        'role',
        'otp',
        'otp_expires_at',
        'device_token'
    ];


    public function isFixedIncome()
    {
        return $this->role === 'fixed_income';
    }

    public function isIrregularIncome()
    {
        return $this->role === 'irregular_income';
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
    //
      public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    //
    public function savings()
    {
        return $this->hasMany(Saving::class);
    }
    //
       public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    //
    public function bonuses(): HasMany
    {
        return $this->hasMany(Bonuses::class, 'user_id')
        ->where('role', 'fixed_income');
    }

  public function budget()
{
    return $this->hasOne(Budget::class);
}
}
