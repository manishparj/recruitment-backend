<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'vacancy_id', 'user_id', 'enabled'
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Get the vacancy that releated to the post.
     */
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    // public function users()
    // {
    //     return $this->hasMany(User::class);
    // }

    public function users()
    {
        return $this->hasMany(User::class);
    }


}
