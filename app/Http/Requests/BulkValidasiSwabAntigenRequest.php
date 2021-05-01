<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkValidasiSwabAntigenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->role_id != ROLE_REGISTER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|array',
            'id.*' => 'required|exists:swab_antigen,id',
            'validator_id' => 'required|exists:validator,id'
        ];
    }
}
