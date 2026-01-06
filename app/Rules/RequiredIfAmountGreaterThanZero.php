<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class RequiredIfAmountGreaterThanZero implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $amount = request('amount'); // Get the 'amount' field from the request
        return $amount > 0 ? !empty($value) : true; // Validate 'value' only if amount > 0
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field is required when the amount is greater than 0.';
    }
}
