<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BooleanTrueFalse implements Rule
{
    public function passes($attribute, $value)
    {
        return in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false'], true);
    }

    public function message()
    {
        return 'The :attribute field must be true or false.';
    }
}
