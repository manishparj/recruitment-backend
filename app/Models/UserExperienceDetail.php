<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExperienceDetail extends Model
{
    use HasFactory;

    protected $table = "user_experience_details";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'experience_type',
        'signatury_designation',
        'employer',
        'designation',
        'department',
        'nature_of_duties',
        'from_date',
        'to_date',
        'presently_working',
        'year_of_experience',
        'remarks',
        'document_path'
    ];

    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'from_date' => 'datetime',
            'to_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
