<?php

namespace App\Http\Requests;

use App\Rules\DocumentOrPath;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class StoreVacancyRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:vacancies,code',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:2024-04-01|before:2028-01-01',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:2024-04-01|before:2028-01-01',
            'posts' => 'required|array',
            'posts.*.name' => 'required|string|max:255',
            'posts.*.code' => 'required|string|max:20',
            'vacancy_docs' => 'required|array',
            'vacancy_docs.*.title' => 'required|string|max:255',
            'vacancy_docs.*.type' => 'required|string|in:notification,shortlist,result,appointment',
            'vacancy_docs.*.document' => ['required', new DocumentOrPath],
            
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
    
}
