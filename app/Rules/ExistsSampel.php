<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Sampel;

class ExistsSampel implements Rule
{

    public $error;
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
        $sampel = Sampel::where('nomor_sampel', 'ilike', $value);
        if ($sampel->doesntExist()) {
            $this->error = 'exists';
        }
        if ($sampel->exists() && $sampel->whereNull('nomor_register')->doesntExist()) {
            $this->error = 'unique';
        }
        return $this->error ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __("validation.{$this->error}", ['attribute' => 'nomor sampel']);
    }
}
