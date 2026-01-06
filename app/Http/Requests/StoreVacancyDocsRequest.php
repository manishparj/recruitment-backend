<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class StoreVacancyDocsRequest extends FormRequest
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
            'vacancy_docs' => 'required|array',
            'vacancy_docs.*.vacancy_id' => 'required|numeric|exists:vacancies_docs,id',
            'vacancy_docs.*.title' => 'required|string|max:255',
            'vacancy_docs.*.type' => 'required|string|in:notification,shortlist,result,appointment',
            'vacancy_docs.*.doc_path' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!Storage::exists($value)) {
                        $fail("The file path does not exist.");
                    }
                },
            ],
        ];
    }
}
