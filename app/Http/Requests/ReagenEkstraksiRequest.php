<?php

namespace App\Http\Requests;

use App\Enums\MetodeEkstraksiEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Enum\Laravel\Rules\EnumValueRule;

class ReagenEkstraksiRequest extends FormRequest
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
                Rule::unique('reagen_ekstraksi')->ignore($this->reagen_ekstraksi)
            ],
            'metode_ekstraksi' => [
                'required',
                new EnumValueRule(MetodeEkstraksiEnum::class)
            ],
        ];
    }
}
