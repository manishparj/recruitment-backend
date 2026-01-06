<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'marital_status',
        'spouse_name',
        'phone',
        'aadhaar_number',
        'date_of_birth',
        'father_name',
        'mother_name',
        'user_category',
        'applied_category',
        'pwd_category',
        'pwd_category_option',
        'ex_service_man',
        'istype_speed_req',
        'correspondence_address',
        'permanent_address',
        'addresses_are_same'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date:Y-m-d',
            'pwd_category' => 'boolean',
            'ex_service_man' => 'boolean',
            'istype_speed_req' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
