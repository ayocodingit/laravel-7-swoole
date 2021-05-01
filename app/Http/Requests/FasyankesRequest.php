<?php

namespace App\Http\Requests;

use App\Enums\FasyankesPengirimEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Enum\Laravel\Rules\EnumRule;

class FasyankesRequest extends FormRequest
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
        return [
            'nama' => [
                'required',
                'max:255',
                Rule::unique('fasyankes')->ignore($this->dinke)
            ],
            'tipe' => [
                'required',
                new EnumRule(FasyankesPengirimEnum::class)
            ],
            'kota_id' => 'nullable|numeric|exists:kota,id'
        ];
    }
}
