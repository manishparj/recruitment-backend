<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\PostBelongsToVacancy;

class StoreUserRequest extends FormRequest
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
            'vacancy_id' => 'required|numeric|exists:vacancies,id',
            //check for post belongs to given vacancies can be allowed
            'post_id' => [
                'required',
                'numeric',
                new PostBelongsToVacancy($this->vacancy_id)
            ],
            'email' => [
                'required',
                'email',
                // Check uniqueness of email within the scope of vacancy_id and post_id
                Rule::unique('users')->where(function ($query) {
                    return $query->where('vacancy_id', $this->vacancy_id)
                                 ->where('post_id', $this->post_id);
                }),
            ],
            'password' => 'required|string|min:8|max:12',
        ];

    }
}
