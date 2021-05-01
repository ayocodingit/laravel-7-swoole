<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenggunaRequest extends FormRequest
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
     * @return array
     */

    public function rules()
    {
        $passwordRule = 'required';
        if ($this->pengguna) {
            $passwordRule = 'nullable';
        }
        return [
            'name' => 'required|max:255',
            'username' => [
                'required',
                'max:80',
                Rule::unique('users')->ignore($this->pengguna)
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->pengguna)
            ],
            'role_id' => 'required|exists:roles,id',
            'validator_id' => 'nullable|exists:validator,id',
            'password' => $passwordRule . '|min:6|confirmed'
        ];
    }
}
