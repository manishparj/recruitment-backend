<?php

namespace App\Http\Requests;

use App\Rules\DocumentOrPath;
use App\Rules\EndDateAfterStartDate;
use DB;
use Illuminate\Foundation\Http\FormRequest;
use Storage;

class UpdateVacancyDataRequest extends FormRequest
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
            
            'vacancy_id' => 'numeric|exists:vacancies,id',
            'vacancy_name' => 'required|string|max:100',
            'vacancy_code' => 'string|max:20',
            'vacancy_start_date' => 'date_format:Y-m-d|after_or_equal:2024-06-01|before:2028-01-01',
            'vacancy_end_date' => ['date_format:Y-m-d','after_or_equal:2024-06-01','before:2028-01-01', new EndDateAfterStartDate($this->start_date)],

            'posts' => 'required|array',
            'posts.*.id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !DB::table('posts')->where('id', $value)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
            'posts.*.name' => 'required|string|max:100',
            'posts.*.code' => 'required|string|max:20',
            'posts.*.is_deleted' => 'required|boolean',

            'vacancy_docs' => 'required|array',
            'vacancy_docs.*.id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !DB::table('vacancies_docs')->where('id', $value)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
            'vacancy_docs.*.title' => 'required|string|max:255',
            'vacancy_docs.*.type' => 'required|string|in:notification,shortlist,result,appointment',
            'vacancy_docs.*.document' => ['required', new DocumentOrPath],
            'vacancy_docs.*.is_deleted' => 'required|boolean',
            
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'start_date.date_format' => 'The start date must be in the format YYYY-MM-DD.',
    //         'start_date.after_or_equal' => 'The start date must be on or after April 1, 2024.',
    //         'start_date.before' => 'The start date must be before January 1, 2028.',
    //         'end_date.date_format' => 'The end date must be in the format YYYY-MM-DD.',
    //         'end_date.after_or_equal' => 'The end date must be on or after April 1, 2024.',
    //         'end_date.before' => 'The end date must be before January 1, 2028.',
    //     ];
    // }

    protected function prepareForValidation()
    {
        // $this->merge([
        //     'user_id' => auth()->id(),
        // ]);
    }

}
