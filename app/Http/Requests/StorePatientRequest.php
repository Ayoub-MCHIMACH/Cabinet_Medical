<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'FullName' => ['required','max:255'],
            'CIN' => ['required'],
            'PhoneNumber' => ['required'],
            'Age' => ['required'],
            'DateOfBirth' => ['required'],
            'Adress' => ['required'],
            'Password' => ['required'],
            'Password_Confirmation' => ['required']
        ];
    }
}
