<?php

namespace App\Http\Requests;

use App\Rules\EndDateAfterStartDate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'numeric|exists:vacancies,id',
            'name' => 'nullable|string|max:100',
            'code' => 'string|max:20',
            'start_date' => 'date_format:Y-m-d|after_or_equal:2024-06-01|before:2028-01-01',
            'end_date' => ['date_format:Y-m-d','after_or_equal:2024-06-01','before:2028-01-01', new EndDateAfterStartDate($this->start_date)]
        ];
    }

    public function messages()
    {
        return [
            'start_date.date_format' => 'The start date must be in the format YYYY-MM-DD.',
            'start_date.after_or_equal' => 'The start date must be on or after April 1, 2024.',
            'start_date.before' => 'The start date must be before January 1, 2028.',
            'end_date.date_format' => 'The end date must be in the format YYYY-MM-DD.',
            'end_date.after_or_equal' => 'The end date must be on or after April 1, 2024.',
            'end_date.before' => 'The end date must be before January 1, 2028.',
        ];
    }
}
