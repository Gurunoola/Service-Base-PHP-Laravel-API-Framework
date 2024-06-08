<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [$this->isPostRequest(), 'string', 'max:255'],
            'email' => [$this->isPostRequest(), 'string', 'email', 'max:255', 'unique:users'],
            'password' => [$this->isPostRequest(), 'confirmed', Rules\Password::defaults()],
            'role' => [$this->isPostRequest(), 'string', 'max:5'],
        ];
    }

    private function isPostRequest()
    {
        return request()->isMethod('post') ? 'required' : 'sometimes';
    }
}
