<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserExistsWithOffset;

class LoginUserRequest extends FormRequest
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
            'id' => ['required', 'numeric', new UserExistsWithOffset()],
            'vacancy_id' => 'required|numeric|exists:vacancies,id',
            'post_id' => 'required|numeric|exists:posts,id',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|max:12',
        ];
    }
}
