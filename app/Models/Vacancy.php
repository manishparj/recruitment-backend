<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $table = 'vacancies';
    
    protected $fillable = [
        'name', 'code', 'start_date', 'end_date', 'user_id', 'enabled'
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Get the posts for the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function vacancyDocs()
    {
        return $this->hasMany(VacancyDoc::class);
    }

    // public function users()
    // {
    //     return $this->hasMany(User::class);
    // }
    

}
