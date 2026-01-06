<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'vacancy_id',
        'post_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function vacancy()
    {
        // return $this->belongsTo(Vacancy::class);
        return $this->belongsTo(Vacancy::class);
    }

    public function post()
    {
        // return $this->belongsTo(Post::class);
        return $this->belongsTo(Post::class);
    }
    // public function post()
    // {
    //     return $this->hasMany(Post::class);
    // }


    public function userDetails()
    {
        return $this->hasOne(UserDetail::class);
    }
    
    public function userEducationDetails()
    {
        // return $this->belongsTo(UserEducationalDetail::class);
        return $this->hasMany(UserEducationalDetail::class);
    }
    public function userExperienceDetails()
    {
        // return $this->belongsTo(UserExperienceDetail::class);
        return $this->hasMany(UserExperienceDetail::class);
    }
    
    public function userDocuments()
    {
        // return $this->belongsTo(UserDocument::class);
        return $this->hasMany(UserDocument::class);
    }

    public function userPaymentDetails()
    {
        // return $this->belongsTo(UserPayment::class);
        return $this->hasMany(UserPayment::class);
    }

}
