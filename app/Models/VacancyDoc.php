<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancyDoc extends Model
{
    use HasFactory;

    protected $table = 'vacancies_docs';

    protected $fillable = [
        'vacancy_id', 'title', 'type', 'doc_path',
    ];

    protected $casts = [

    ];


    public function vacancyDocs()
    {
        return $this->belongsTo(Vacancy::class);
    }


}
