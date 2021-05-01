<?php

namespace App\Rules;

use App\Enums\JenisRegistrasiEnum;
use App\Models\TesMasif;
use Illuminate\Contracts\Validation\Rule;

class TesMasifRujukanExistsRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->nomor_sampel = $value;
        return TesMasif::where('nomor_sampel', $value)
            ->where('available', true)
            ->where('jenis_registrasi', JenisRegistrasiEnum::rujukan())
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.exists', ['attribute' => $this->nomor_sampel]);
    }
}
