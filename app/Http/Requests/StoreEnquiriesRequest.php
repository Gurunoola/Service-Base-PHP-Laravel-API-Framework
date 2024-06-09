<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Base64Image;
class StoreEnquiriesRequest extends FormRequest
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
            'name' => [$this->isPostRequest(), 'unique:users,name', 'max:255'],
            'gender' => [$this->isPostRequest(), 'max:10'],
            'dob' => [$this->isPostRequest(), 'date'],
            'phone_number' => [$this->isPostRequest(), 'numeric', 'digits:10'],
            'alternate_phone_number' => ['nullable', 'numeric', 'digits:10'],
            'email' => [$this->isPostRequest(), 'string', 'email', 'max:255'],
            'address' => [$this->isPostRequest(), 'max:255'],
            'qualification' => [$this->isPostRequest(), 'max:255'],
            'course' => [$this->isPostRequest(), 'max:255'],
            'optional_subject' => ['nullable', 'max:255'],
            'attempts_given' => [$this->isPostRequest(), 'integer'],
            'referral_source' => [$this->isPostRequest(), 'max:255'],
            'counseling_satisfaction' => [$this->isPostRequest(), 'max:255'],
            'contact_preference' => [$this->isPostRequest(), 'boolean'],
            'counsellor_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'max:255'],
            'rescheduled_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'max:255'],
            'dp_path' => ['nullable', new Base64Image]
        ];
    }

    private function isPostRequest()
    {
        return request()->isMethod('post') ? 'required' : 'sometimes';
    }
}
