<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducationalDetail extends Model
{
    use HasFactory;

    protected $table = "user_educational_details";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'exam_name',
        'subject_names',
        'institute_name',
        'roll_number',
        'result_type',
        'result',
        'year_of_passed',
        'remarks',
        'enabled',
    ];

    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
