<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PostBelongsToVacancy implements Rule
{
    protected $vacancyId;

    public function __construct($vacancyId)
    {
        $this->vacancyId = $vacancyId;
    }

    public function passes($attribute, $value)
    {
        return DB::table('posts')
            ->where('id', $value)
            ->where('vacancy_id', $this->vacancyId)
            ->exists();
    }

    public function message()
    {
        return 'The selected post does not belong to the given vacancy.';
    }
}
